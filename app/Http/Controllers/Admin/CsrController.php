<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CsrItem;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;

class CsrController extends Controller
{
    public function index()
    {
        $menu = Menu::where('slug', 'csr-list')->firstOrFail();

        $groupedCsr = CsrItem::orderBy('csr_date', 'desc')
            ->orderBy('order', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->csr_date)->format('Y-m-d');
            });

        return view('admin.csr-list.index', compact('menu', 'groupedCsr'));
    }

    private function generateUniqueSlug($title, $ignoreId = null)
    {
        $baseSlug = \Illuminate\Support\Str::slug($title);
        $slug = $baseSlug;
        $counter = 2;

        while (
            CsrItem::where('slug', $slug)
                ->when($ignoreId, function ($query) use ($ignoreId) {
                    return $query->where('id', '!=', $ignoreId);
                })
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'csr_date' => 'required|date',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:102400',
        ]);

        try {
            $slug = $this->generateUniqueSlug($request->title);
            $path = "csr/{$slug}.webp";

            if (!Storage::disk('public')->exists('csr')) {
                Storage::disk('public')->makeDirectory('csr');
            }

            $this->processCsrImage(
                $request->file('image')->getRealPath(),
                storage_path("app/public/{$path}")
            );

            CsrItem::create([
                'title' => $request->title,
                'slug' => $slug,
                'description' => $request->description,
                'csr_date' => $request->csr_date,
                'image_path' => $path,
                'is_active' => 1,
                'order' => (CsrItem::where('csr_date', $request->csr_date)->max('order') ?? 0) + 1
            ]);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, CsrItem $csrItem)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'csr_date' => 'required|date',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:102400',
            'is_active' => 'required'
        ]);

        try {
            $slug = $csrItem->slug;
            if ($request->title !== $csrItem->title) {
                $slug = $this->generateUniqueSlug($request->title, $csrItem->id);
            }

            $data = [
                'title' => $request->title,
                'slug' => $slug,
                'description' => $request->description,
                'csr_date' => $request->csr_date,
                'is_active' => $request->is_active,
            ];

            if ($request->hasFile('image')) {
                if (Storage::disk('public')->exists($csrItem->image_path)) {
                    Storage::disk('public')->delete($csrItem->image_path);
                }

                $path = "csr/{$slug}.webp";
                $this->processCsrImage(
                    $request->file('image')->getRealPath(),
                    storage_path("app/public/{$path}")
                );

                $data['image_path'] = $path;
            }

            $oldSlug = $csrItem->slug;

            if (
                $slug !== $oldSlug &&
                !$request->hasFile('file') &&
                $csrItem->file_path
            ) {
                $oldPath = $csrItem->file_path;
                $ext = pathinfo($oldPath, PATHINFO_EXTENSION);
                $newPath = "news/{$slug}.{$ext}";

                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->move($oldPath, $newPath);
                    $data['file_path'] = $newPath;
                }
            }

            $csrItem->update($data);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function processCsrImage($sourcePath, $destinationPath)
    {
        ini_set('memory_limit', '1024M');

        if (!extension_loaded('gd')) {
            throw new Exception('GD library is not installed or enabled.');
        }

        $info = getimagesize($sourcePath);
        if (!$info) {
            throw new Exception('Invalid image file.');
        }

        $width = $info[0];
        $height = $info[1];
        $type = $info[2];

        switch ($type) {
            case IMAGETYPE_JPEG:
                $src = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $src = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_WEBP:
                $src = imagecreatefromwebp($sourcePath);
                break;
            default:
                throw new Exception('Unsupported image type.');
        }

        if (!$src) {
            throw new Exception('Failed to load image resource.');
        }

        imagepalettetotruecolor($src);
        imagealphablending($src, true);
        imagesavealpha($src, true);

        $targetRatio = 16 / 9;
        $currentRatio = $width / $height;

        if ($currentRatio > $targetRatio) {
            $cropWidth = $height * $targetRatio;
            $cropHeight = $height;
            $srcX = ($width - $cropWidth) / 2;
            $srcY = 0;
        } else {
            $cropWidth = $width;
            $cropHeight = $width / $targetRatio;
            $srcX = 0;
            $srcY = ($height - $cropHeight) / 2;
        }

        $finalWidth = $cropWidth;
        if ($finalWidth > 2000) {
            $finalWidth = 2000;
        }
        $finalHeight = $finalWidth / $targetRatio;

        $dst = imagecreatetruecolor($finalWidth, $finalHeight);

        imagealphablending($dst, false);
        imagesavealpha($dst, true);

        imagecopyresampled($dst, $src, 0, 0, $srcX, $srcY, $finalWidth, $finalHeight, $cropWidth, $cropHeight);

        if (!imagewebp($dst, $destinationPath, 70)) {
            throw new Exception('Failed to save WebP image.');
        }

        imagedestroy($src);
        imagedestroy($dst);
    }

    public function updateOrder(Request $request)
    {
        foreach ($request->orders as $item) {
            CsrItem::where('id', $item['id'])->update(['order' => $item['order']]);
        }
        return response()->json(['success' => true]);
    }

    public function delete(CsrItem $csrItem)
    {
        try {
            if (Storage::disk('public')->exists($csrItem->image_path)) {
                Storage::disk('public')->delete($csrItem->image_path);
            }

            $csrItem->delete();

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function serveCsrImage($filename)
    {
        $storagePath = storage_path('app/public/csr/' . $filename);
        abort_if(!file_exists($storagePath), 404);
        return response()->file($storagePath);
    }

    public function frontendIndex($menu)
    {
        $items = CsrItem::where('is_active', 1)
            ->orderBy('csr_date', 'desc')
            ->orderBy('order', 'desc')
            ->paginate(9);

        return view('csr.index', compact('items', 'menu'));
    }

    public function frontendShow($menu, $slug)
    {
        $item = CsrItem::where('slug', $slug)->where('is_active', 1)->firstOrFail();

        $related = CsrItem::where('is_active', 1)
            ->where('id', '!=', $item->id)
            ->latest('csr_date')
            ->take(5)
            ->get();

        return view('csr.show', compact('item', 'related', 'menu'));
    }
}