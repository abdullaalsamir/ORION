<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsItem;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;
use Str;

class NewsController extends Controller
{
    public function index()
    {
        $menu = Menu::where('slug', 'news-and-announcements')->firstOrFail();

        $groupedNews = NewsItem::orderBy('news_date', 'desc')
            ->orderBy('order', 'desc')
            ->get()
            ->groupBy(fn($item) => \Carbon\Carbon::parse($item->news_date)->format('Y-m-d'));

        return view('admin.news-and-announcements.index', compact('menu', 'groupedNews'));
    }

    private function generateUniqueSlug($title, $ignoreId = null)
    {
        $baseSlug = \Illuminate\Support\Str::slug($title);
        $slug = $baseSlug;
        $counter = 2;

        while (
            NewsItem::where('slug', $slug)
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
            'news_date' => 'required|date',
            'file' => 'required|mimes:jpg,jpeg,png,webp,pdf|max:102400',
        ]);

        try {
            $slug = $this->generateUniqueSlug($request->title);

            $file = $request->file('file');
            $mime = $file->getMimeType();

            if ($mime === 'application/pdf') {
                $fileType = 'pdf';
                $ext = 'pdf';
            } elseif (str_starts_with($mime, 'image/')) {
                $fileType = 'image';
                $ext = 'webp';
            } else {
                abort(422, 'Unsupported file type');
            }

            $path = "news/{$slug}.{$ext}";

            Storage::disk('public')->makeDirectory('news');

            if ($fileType === 'image') {
                $this->processNewsImage(
                    $file->getRealPath(),
                    storage_path("app/public/{$path}")
                );
            } else {
                $file->storeAs('news', "{$slug}.{$ext}", 'public');
            }

            NewsItem::create([
                'title' => $request->title,
                'slug' => $slug,
                'description' => $request->description,
                'news_date' => $request->news_date,
                'file_type' => $fileType,
                'file_path' => $path,
                'is_active' => 1,
                'is_pin' => $request->boolean('is_pin'),
                'order' => (NewsItem::where('news_date', $request->news_date)->max('order') ?? 0) + 1,
            ]);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, NewsItem $newsItem)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'news_date' => 'required|date',
            'file' => 'nullable|mimes:jpg,jpeg,png,webp,pdf|max:102400',
            'is_active' => 'required'
        ]);

        try {
            $slug = $newsItem->slug;
            if ($request->title !== $newsItem->title) {
                $slug = $this->generateUniqueSlug($request->title, $newsItem->id);
            }

            $data = [
                'title' => $request->title,
                'slug' => $slug,
                'description' => $request->description,
                'news_date' => $request->news_date,
                'is_pin' => $request->boolean('is_pin'),
                'is_active' => $request->boolean('is_active'),
            ];

            if ($request->hasFile('file')) {
                if (Storage::disk('public')->exists($newsItem->file_path)) {
                    Storage::disk('public')->delete($newsItem->file_path);
                }

                $file = $request->file('file');
                $mime = $file->getMimeType();

                if ($mime === 'application/pdf') {
                    $fileType = 'pdf';
                    $ext = 'pdf';
                } elseif (str_starts_with($mime, 'image/')) {
                    $fileType = 'image';
                    $ext = 'webp';
                } else {
                    abort(422, 'Unsupported file type');
                }

                $path = "news/{$slug}.{$ext}";

                if ($fileType === 'image') {
                    $this->processNewsImage(
                        $file->getRealPath(),
                        storage_path("app/public/{$path}")
                    );
                } else {
                    $file->storeAs('news', "{$slug}.{$ext}", 'public');
                }

                $data['file_type'] = $fileType;
                $data['file_path'] = $path;
            }

            $oldSlug = $newsItem->slug;

            if (
                $slug !== $oldSlug &&
                !$request->hasFile('file') &&
                $newsItem->file_path
            ) {
                $oldPath = $newsItem->file_path;
                $ext = pathinfo($oldPath, PATHINFO_EXTENSION);
                $newPath = "news/{$slug}.{$ext}";

                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->move($oldPath, $newPath);
                    $data['file_path'] = $newPath;
                }
            }

            $newsItem->update($data);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function processNewsImage($sourcePath, $destinationPath)
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
            NewsItem::where('id', $item['id'])->update(['order' => $item['order']]);
        }
        return response()->json(['success' => true]);
    }

    public function delete(NewsItem $newsItem)
    {
        try {
            if (Storage::disk('public')->exists($newsItem->file_path)) {
                Storage::disk('public')->delete($newsItem->file_path);
            }
            $newsItem->delete();
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function serveNewsImage($filename)
    {
        $storagePath = storage_path('app/public/news/' . $filename);
        abort_if(!file_exists($storagePath), 404);
        return response()->file($storagePath);
    }

    public function serveNewsPdf($path, $filename)
    {
        $storagePath = storage_path('app/public/news/' . $filename);

        if (!file_exists($storagePath)) {
            abort(404, "PDF not found in storage.");
        }

        return response()->file($storagePath, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    public function frontendIndex($menu)
    {
        $pinned = NewsItem::where('is_active', 1)
            ->where('is_pin', 1)
            ->orderBy('news_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        $items = NewsItem::where('is_active', 1)
            ->orderBy('news_date', 'desc')
            ->orderBy('order', 'desc')
            ->paginate(5);

        return view('news.index', compact('items', 'menu', 'pinned'));
    }

    public function frontendShow($menu, $slug)
    {
        $item = NewsItem::where('slug', $slug)->where('is_active', 1)->firstOrFail();

        $related = NewsItem::where('is_active', 1)
            ->where('id', '!=', $item->id)
            ->latest('news_date')
            ->take(5)
            ->get();

        return view('news.show', compact('item', 'related', 'menu'));
    }
}