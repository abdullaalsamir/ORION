<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoardDirector;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;

class BoardDirectorController extends Controller
{
    public function index()
    {
        $menu = Menu::where('slug', 'board-of-directors')->firstOrFail();
        $items = BoardDirector::orderBy('order', 'asc')->get();
        return view('admin.board-of-directors.index', compact('menu', 'items'));
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
            $path = "directors/{$fileName}";

            if (!Storage::disk('public')->exists('directors')) {
                Storage::disk('public')->makeDirectory('directors');
            }

            $this->processDirectorImage($request->file('image')->getRealPath(), storage_path("app/public/{$path}"));

            BoardDirector::create([
                'name' => $request->name,
                'slug' => $slug,
                'designation' => $request->designation,
                'description' => $request->description,
                'image_path' => $path,
                'is_active' => 1,
                'order' => (BoardDirector::max('order') ?? 0) + 1
            ]);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, BoardDirector $boardDirector)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'designation' => 'required|string|max:100',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:102400',
            'is_active' => 'required'
        ]);

        try {
            $slug = $boardDirector->slug;
            if ($request->name !== $boardDirector->name) {
                $slug = $this->generateUniqueSlug($request->name, $boardDirector->id);
            }

            $data = [
                'name' => $request->name,
                'slug' => $slug,
                'designation' => $request->designation,
                'description' => $request->description,
                'is_active' => $request->is_active
            ];

            if ($request->hasFile('image')) {
                if (Storage::disk('public')->exists($boardDirector->image_path)) {
                    Storage::disk('public')->delete($boardDirector->image_path);
                }
                $path = "directors/{$slug}.webp";
                $this->processDirectorImage($request->file('image')->getRealPath(), storage_path("app/public/{$path}"));
                $data['image_path'] = $path;
            }

            $boardDirector->update($data);
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
            BoardDirector::where('slug', $slug)
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

    private function processDirectorImage($sourcePath, $destinationPath)
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
            BoardDirector::where('id', $item['id'])->update(['order' => $item['order']]);
        }
        return response()->json(['success' => true]);
    }

    public function delete(BoardDirector $boardDirector)
    {
        try {
            if (Storage::disk('public')->exists($boardDirector->image_path)) {
                Storage::disk('public')->delete($boardDirector->image_path);
            }
            $boardDirector->delete();
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function serveImage($filename)
    {
        $storagePath = storage_path('app/public/directors/' . $filename);
        abort_if(!file_exists($storagePath), 404);
        return response()->file($storagePath);
    }

    public function frontendIndex($menu)
    {
        $items = BoardDirector::where('is_active', 1)->orderBy('order', 'asc')->get();
        return view('board-of-directors.index', compact('items', 'menu'));
    }

    public function frontendShow($menu, $slug)
    {
        $item = BoardDirector::where('slug', $slug)->where('is_active', 1)->firstOrFail();
        return view('board-of-directors.show', compact('item', 'menu'));
    }
}