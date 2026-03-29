<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;

class BannerController extends Controller
{
    public function index()
    {
        $leafMenus = Menu::getFunctionalLeafMenus()
            ->whereNotIn('slug', ['csr', 'career', 'photo-gallery']);

        return view('admin.banners.index', compact('leafMenus'));
    }

    public function getBanners(Menu $menu)
    {
        $banners = $menu->banners()->orderBy('created_at', 'asc')->get();
        return response()->json([
            'html' => view('admin.banners.partials.banner-list', compact('banners', 'menu'))->render()
        ]);
    }

    public function store(Request $request, Menu $menu)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:102400',
            'ratio' => 'required|integer|min:0|max:2',
            'max_width' => 'required|integer|min:500|max:2000'
        ]);

        try {
            $file = $request->file('image');
            $baseName = time();
            $fileName = $baseName . '.webp';
            $relativeDir = "banners/{$menu->full_slug}";
            $thumbDir = "{$relativeDir}/thumbs";

            if (!Storage::disk('public')->exists($relativeDir)) {
                Storage::disk('public')->makeDirectory($relativeDir);
            }
            if (!Storage::disk('public')->exists($thumbDir)) {
                Storage::disk('public')->makeDirectory($thumbDir);
            }

            $fullPath = storage_path("app/public/{$relativeDir}/{$fileName}");
            $thumb700Path = storage_path("app/public/{$thumbDir}/{$baseName}-700.webp");
            $thumb250Path = storage_path("app/public/{$thumbDir}/{$baseName}-250.webp");

            $ratios = [48 / 9, 23 / 9, 16 / 9];
            $targetRatio = $ratios[$request->ratio];

            list($finalWidth, $finalHeight) = $this->processBanner($file->getRealPath(), $fullPath, $targetRatio, $request->max_width);
            $this->processBanner($file->getRealPath(), $thumb700Path, $targetRatio, 700);
            $this->processBanner($file->getRealPath(), $thumb250Path, $targetRatio, 250);

            Banner::create([
                'menu_id' => $menu->id,
                'file_name' => $fileName,
                'file_path' => "{$relativeDir}/{$fileName}",
                'is_active' => 1,
                'image_width' => (int) $finalWidth,
                'image_height' => (int) $finalHeight
            ]);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate(['is_active' => 'required']);
        try {
            $banner->is_active = $request->is_active;
            $banner->save();
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function processBanner($sourcePath, $destinationPath, $targetRatio, $maxWidth)
    {
        ini_set('memory_limit', '1024M');
        if (!extension_loaded('gd'))
            throw new Exception('GD library is not installed or enabled.');

        $info = getimagesize($sourcePath);
        if (!$info)
            throw new Exception('Invalid image file.');

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

        if (!$src)
            throw new Exception('Failed to load image resource.');

        imagepalettetotruecolor($src);
        imagealphablending($src, true);
        imagesavealpha($src, true);

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
        if ($finalWidth > $maxWidth)
            $finalWidth = $maxWidth;
        $finalHeight = $finalWidth / $targetRatio;

        $dst = imagecreatetruecolor($finalWidth, $finalHeight);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, $srcX, $srcY, $finalWidth, $finalHeight, $cropWidth, $cropHeight);

        if (!imagewebp($dst, $destinationPath, 70))
            throw new Exception('Failed to save WebP image.');
        imagedestroy($src);
        imagedestroy($dst);

        return [$finalWidth, $finalHeight];
    }

    public function delete(Banner $banner)
    {
        if (Storage::disk('public')->exists($banner->file_path)) {
            Storage::disk('public')->delete($banner->file_path);
        }

        $baseName = str_replace('.webp', '', $banner->file_name);
        $thumbDir = dirname($banner->file_path) . '/thumbs';

        if (Storage::disk('public')->exists("{$thumbDir}/{$baseName}-700.webp")) {
            Storage::disk('public')->delete("{$thumbDir}/{$baseName}-700.webp");
        }
        if (Storage::disk('public')->exists("{$thumbDir}/{$baseName}-250.webp")) {
            Storage::disk('public')->delete("{$thumbDir}/{$baseName}-250.webp");
        }

        $banner->delete();
        return response()->json(['success' => true]);
    }

    public function getBannersForEditor(Menu $menu)
    {
        $banners = $menu->banners()->where('is_active', 1)->orderBy('created_at', 'asc')->get();

        return response()->json($banners->map(function ($banner) {
            $baseName = str_replace('.webp', '', $banner->file_name);
            $thumbDir = dirname($banner->file_path) . '/thumbs';

            return [
                'url' => asset('storage/' . $banner->file_path),
                'thumb_url' => asset('storage/' . $thumbDir . '/' . $baseName . '-250.webp'),
                'name' => $banner->file_name,
                'width' => $banner->image_width,
                'height' => $banner->image_height
            ];
        }));
    }
}