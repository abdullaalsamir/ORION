<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VideoGallery;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;

class VideoGalleryController extends Controller
{
    public function index()
    {
        $menu = Menu::where('slug', 'video-gallery')->firstOrFail();
        $items = VideoGallery::orderBy('order', 'asc')->get();
        return view('admin.video-gallery.index', compact('menu', 'items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'video' => 'required|file|mimetypes:video/*|max:204800',
            'thumbnail' => 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        try {
            if (!Storage::disk('public')->exists('video-gallery/videos')) {
                Storage::disk('public')->makeDirectory('video-gallery/videos');
                Storage::disk('public')->makeDirectory('video-gallery/thumbnails');
            }

            $epoch = time() . '_' . uniqid();

            $videoName = $epoch . '.' . $request->file('video')->getClientOriginalExtension();
            $videoPath = $request->file('video')->storeAs('video-gallery/videos', $videoName, 'public');

            $thumbName = $epoch . '.webp';
            $thumbPath = "video-gallery/thumbnails/{$thumbName}";
            $this->processVideoThumbnail($request->file('thumbnail')->getRealPath(), storage_path("app/public/{$thumbPath}"));

            VideoGallery::create([
                'title' => $request->title,
                'video_path' => $videoPath,
                'thumbnail_path' => $thumbPath,
                'is_active' => 1,
                'order' => (VideoGallery::max('order') ?? 0) + 1
            ]);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, VideoGallery $videoGallery)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'video' => 'nullable|file|mimetypes:video/*|max:204800',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'is_active' => 'required|boolean'
        ]);

        try {
            $data = [
                'title' => $request->title,
                'is_active' => $request->is_active
            ];

            $epoch = time() . '_' . uniqid();

            if ($request->hasFile('video')) {
                if (Storage::disk('public')->exists($videoGallery->video_path)) {
                    Storage::disk('public')->delete($videoGallery->video_path);
                }
                $videoName = $epoch . '.' . $request->file('video')->getClientOriginalExtension();
                $data['video_path'] = $request->file('video')->storeAs('video-gallery/videos', $videoName, 'public');
            }

            if ($request->hasFile('thumbnail')) {
                if (Storage::disk('public')->exists($videoGallery->thumbnail_path)) {
                    Storage::disk('public')->delete($videoGallery->thumbnail_path);
                }
                $thumbName = $epoch . '.webp';
                $thumbPath = "video-gallery/thumbnails/{$thumbName}";
                $this->processVideoThumbnail($request->file('thumbnail')->getRealPath(), storage_path("app/public/{$thumbPath}"));
                $data['thumbnail_path'] = $thumbPath;
            }

            $videoGallery->update($data);
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function delete(VideoGallery $videoGallery)
    {
        try {
            if (Storage::disk('public')->exists($videoGallery->video_path)) {
                Storage::disk('public')->delete($videoGallery->video_path);
            }
            if (Storage::disk('public')->exists($videoGallery->thumbnail_path)) {
                Storage::disk('public')->delete($videoGallery->thumbnail_path);
            }
            $videoGallery->delete();
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function updateOrder(Request $request)
    {
        foreach ($request->orders as $item) {
            VideoGallery::where('id', $item['id'])->update(['order' => $item['order']]);
        }
        return response()->json(['success' => true]);
    }

    public function serveVideo($filename)
    {
        $path = storage_path('app/public/video-gallery/videos/' . $filename);
        abort_if(!file_exists($path), 404);
        return response()->file($path);
    }

    public function serveThumbnail($filename)
    {
        $path = storage_path('app/public/video-gallery/thumbnails/' . $filename);
        abort_if(!file_exists($path), 404);
        return response()->file($path);
    }

    public function frontendIndex($menu)
    {
        $videos = VideoGallery::where('is_active', 1)->orderBy('order', 'asc')->get();
        return view('video-gallery.index', compact('videos', 'menu'));
    }

    private function processVideoThumbnail($sourcePath, $destinationPath)
    {
        ini_set('memory_limit', '1024M');
        if (!extension_loaded('gd'))
            throw new Exception('GD library is not enabled.');

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

        $finalWidth = $cropWidth > 1920 ? 1920 : $cropWidth;
        $finalHeight = $finalWidth / $targetRatio;

        $dst = imagecreatetruecolor($finalWidth, $finalHeight);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);

        imagecopyresampled($dst, $src, 0, 0, $srcX, $srcY, $finalWidth, $finalHeight, $cropWidth, $cropHeight);

        if (!imagewebp($dst, $destinationPath, 75))
            throw new Exception('Failed to save WebP thumbnail.');

        imagedestroy($src);
        imagedestroy($dst);
    }
}