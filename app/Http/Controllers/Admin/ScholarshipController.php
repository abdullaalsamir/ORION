<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class ScholarshipController extends Controller
{
    public function index()
    {
        $menu = Menu::where('slug', 'scholarship')->firstOrFail();
        $items = Scholarship::orderBy('order', 'asc')->get();
        return view('admin.scholarship.index', compact('menu', 'items'));
    }

    private function getAvailableFileName($name, $currentPath = null)
    {
        $baseSlug = Str::slug($name);
        $extension = '.webp';
        $directory = 'scholarship';

        $i = 1;
        while (true) {
            $targetName = ($i === 1) ? $baseSlug : $baseSlug . '-' . $i;
            $targetPath = "{$directory}/{$targetName}{$extension}";

            if ($currentPath && $targetPath === $currentPath) {
                return $targetName . $extension;
            }

            if (!Storage::disk('public')->exists($targetPath)) {
                return $targetName . $extension;
            }
            $i++;
        }
    }
    private function formatPrefix($value, $prefix)
    {
        if (empty($value))
            return null;
        $cleanValue = str_replace($prefix . ': ', '', $value);
        $cleanValue = trim($cleanValue);
        if (empty($cleanValue))
            return null;
        return $prefix . ': ' . $cleanValue;
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'degree' => 'required|string',
            'medical_college' => 'required|string',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:102400'
        ]);

        try {
            $file = $request->file('image');

            $fileName = $this->getAvailableFileName($request->name);
            $path = "scholarship/{$fileName}";

            if (!Storage::disk('public')->exists('scholarship')) {
                Storage::disk('public')->makeDirectory('scholarship');
            }

            $this->processScholarImage($file->getRealPath(), storage_path("app/public/{$path}"));

            Scholarship::create([
                'name' => $request->name,
                'degree' => $request->degree,
                'session' => $this->formatPrefix($request->input('session'), 'Session'),
                'medical_college' => $request->medical_college,
                'image_path' => $path,
                'order' => (Scholarship::max('order') ?? 0) + 1,
                'is_active' => 1
            ]);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Scholarship $scholarship)
    {
        try {
            $oldPath = $scholarship->image_path;

            $newFileName = $this->getAvailableFileName($request->name, $oldPath);
            $newPath = "scholarship/{$newFileName}";

            $data = [
                'name' => $request->name,
                'degree' => $request->degree,
                'session' => $this->formatPrefix($request->input('session'), 'Session'),
                'medical_college' => $request->medical_college,
                'is_active' => $request->is_active,
                'image_path' => $newPath
            ];

            if ($request->hasFile('image')) {
                Storage::disk('public')->delete($oldPath);
                $this->processScholarImage($request->file('image')->getRealPath(), storage_path("app/public/{$newPath}"));
            } elseif ($oldPath !== $newPath) {
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->move($oldPath, $newPath);
                }
            }

            $scholarship->update($data);
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function updateOrder(Request $request)
    {
        foreach ($request->orders as $item) {
            Scholarship::where('id', $item['id'])->update(['order' => $item['order']]);
        }
        return response()->json(['success' => true]);
    }

    public function delete(Scholarship $scholarship)
    {
        if (Storage::disk('public')->exists($scholarship->image_path)) {
            Storage::disk('public')->delete($scholarship->image_path);
        }

        $scholarship->delete();

        return response()->json(['success' => true]);
    }

    private function processScholarImage($sourcePath, $destinationPath)
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

        $targetRatio = 9 / 11;
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
        if ($finalWidth > 300) {
            $finalWidth = 300;
        }
        $finalHeight = $finalWidth / $targetRatio;

        $dst = imagecreatetruecolor($finalWidth, $finalHeight);

        imagealphablending($dst, false);
        imagesavealpha($dst, true);

        imagecopyresampled($dst, $src, 0, 0, $srcX, $srcY, $finalWidth, $finalHeight, $cropWidth, $cropHeight);

        if (!imagewebp($dst, $destinationPath, 100)) {
            throw new Exception('Failed to save WebP image.');
        }

        imagedestroy($src);
        imagedestroy($dst);
    }

    public function frontendIndex($menu)
    {
        $items = Scholarship::where('is_active', 1)
            ->orderBy('order', 'desc')
            ->paginate(10);

        return view('scholarship.index', compact('items', 'menu'));
    }

    public function serveScholarImage($filename)
    {
        $storagePath = storage_path('app/public/scholarship/' . $filename);
        abort_if(!file_exists($storagePath), 404);
        return response()->file($storagePath);
    }
}