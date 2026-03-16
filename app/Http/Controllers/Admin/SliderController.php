<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::orderBy('order')->get();
        $menus = Menu::whereNull('parent_id')->where('slug', '!=', 'home')->orderBy('order')->with('children.children')->get();
        $allMenus = Menu::all();
        return view('admin.sliders.index', compact('sliders', 'menus', 'allMenus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:102400',
            'header_1' => 'required|string|max:220',
            'header_2' => 'required|string|max:220',
            'description' => 'nullable|string|max:1500',
            'button_text' => 'nullable|string|max:150',
            'link_url' => 'nullable|string',
        ]);

        try {
            $file = $request->file('image');
            $fileName = time() . '.webp';
            $path = "sliders/{$fileName}";
            $this->processSliderImage($file->getRealPath(), storage_path("app/public/{$path}"));

            $link = $request->filled('link_url') ? $request->link_url : null;
            $button = $link ? ($request->input('button_text') ?? 'Explore More') : null;

            Slider::create([
                'image_path' => $path,
                'header_1' => $request->header_1,
                'header_2' => $request->header_2,
                'description' => $request->description,
                'button_text' => $button,
                'link_url' => $link,
                'order' => (Slider::max('order') ?? 0) + 1
            ]);
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Slider $slider)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:102400',
            'header_1' => 'required|string|max:220',
            'header_2' => 'required|string|max:220',
            'description' => 'nullable|string|max:1500',
            'button_text' => 'nullable|string|max:150',
            'link_url' => 'nullable|string',
            'is_active' => 'required'
        ]);

        try {
            if ($request->hasFile('image')) {
                Storage::disk('public')->delete($slider->image_path);
                $this->processSliderImage($request->file('image')->getRealPath(), storage_path("app/public/{$slider->image_path}"));
            }

            $link = $request->filled('link_url') ? $request->link_url : null;
            $button = $link ? ($request->input('button_text') ?? 'Explore More') : null;

            $slider->update([
                'header_1' => $request->header_1,
                'header_2' => $request->header_2,
                'description' => $request->description,
                'button_text' => $button,
                'link_url' => $link,
                'is_active' => $request->is_active
            ]);
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function updateOrder(Request $request)
    {
        foreach ($request->orders as $item)
            Slider::where('id', $item['id'])->update(['order' => $item['order']]);
        return response()->json(['success' => true]);
    }

    public function delete(Slider $slider)
    {
        $currentOrder = $slider->order;
        if (Storage::disk('public')->exists($slider->image_path))
            Storage::disk('public')->delete($slider->image_path);
        $slider->delete();
        Slider::where('order', '>', $currentOrder)->decrement('order');
        return response()->json(['success' => true, 'message' => 'Slider deleted successfully']);
    }

    private function processSliderImage($sourcePath, $destinationPath)
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

        imagepalettetotruecolor($src);
        imagealphablending($src, true);
        imagesavealpha($src, true);

        $targetRatio = 23 / 9;
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
        if ($finalWidth > 2000)
            $finalWidth = 2000;
        $finalHeight = $finalWidth / $targetRatio;

        $dst = imagecreatetruecolor($finalWidth, $finalHeight);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, $srcX, $srcY, $finalWidth, $finalHeight, $cropWidth, $cropHeight);
        if (!imagewebp($dst, $destinationPath, 70))
            throw new Exception('Failed to save WebP image.');
        imagedestroy($src);
        imagedestroy($dst);
    }
}