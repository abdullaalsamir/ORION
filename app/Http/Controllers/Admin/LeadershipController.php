<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leadership;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;

class LeadershipController extends Controller
{
    public function index()
    {
        $menu = Menu::where('slug', 'leadership')->firstOrFail();
        $items = Leadership::orderBy('order', 'asc')->get();
        return view('admin.leadership.index', compact('menu', 'items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'designation' => 'required|string|max:100',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:102400',
        ]);

        try {
            $slug = $this->generateUniqueSlug($request->name);
            $fileName = $slug . '.webp';
            $path = "leadership/{$fileName}";

            if (!Storage::disk('public')->exists('leadership')) {
                Storage::disk('public')->makeDirectory('leadership');
            }

            $this->processLeadershipImage($request->file('image')->getRealPath(), storage_path("app/public/{$path}"));

            Leadership::create([
                'name' => $request->name,
                'slug' => $slug,
                'designation' => $request->designation,
                'description' => $request->description,
                'image_path' => $path,
                'is_active' => 1,
                'order' => (Leadership::max('order') ?? 0) + 1
            ]);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Leadership $leadership)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'designation' => 'required|string|max:100',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:102400',
            'is_active' => 'required'
        ]);

        try {
            $slug = $leadership->slug;
            if ($request->name !== $leadership->name) {
                $slug = $this->generateUniqueSlug($request->name, $leadership->id);
            }

            $data = [
                'name' => $request->name,
                'slug' => $slug,
                'designation' => $request->designation,
                'description' => $request->description,
                'is_active' => $request->is_active
            ];

            if ($request->hasFile('image')) {
                if (Storage::disk('public')->exists($leadership->image_path)) {
                    Storage::disk('public')->delete($leadership->image_path);
                }
                $path = "leadership/{$slug}.webp";
                $this->processLeadershipImage($request->file('image')->getRealPath(), storage_path("app/public/{$path}"));
                $data['image_path'] = $path;
            }

            $leadership->update($data);
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function generateUniqueSlug($name, $ignoreId = null)
    {
        $baseSlug = \Illuminate\Support\Str::slug($name);
        $slug = $baseSlug;
        $counter = 2;

        while (
            Leadership::where('slug', $slug)
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

    private function processLeadershipImage($sourcePath, $destinationPath)
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

        $targetRatio = 3 / 4;
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
        if ($finalWidth > 1000) {
            $finalWidth = 1000;
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
            Leadership::where('id', $item['id'])->update(['order' => $item['order']]);
        }
        return response()->json(['success' => true]);
    }

    public function delete(Leadership $leadership)
    {
        try {
            if (Storage::disk('public')->exists($leadership->image_path)) {
                Storage::disk('public')->delete($leadership->image_path);
            }
            $leadership->delete();
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function serveImage($filename)
    {
        $storagePath = storage_path('app/public/leadership/' . $filename);
        abort_if(!file_exists($storagePath), 404);
        return response()->file($storagePath);
    }

    public function frontendIndex($menu)
    {
        $items = Leadership::where('is_active', 1)->orderBy('order', 'asc')->get();
        return view('leadership.index', compact('items', 'menu'));
    }

    public function frontendShow($menu, $slug)
    {
        $item = Leadership::where('slug', $slug)->where('is_active', 1)->firstOrFail();
        return view('leadership.show', compact('item', 'menu'));
    }
}