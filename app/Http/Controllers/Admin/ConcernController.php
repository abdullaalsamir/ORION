<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Concern;
use App\Models\ConcernGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;

class ConcernController extends Controller
{
    public function index()
    {
        $parentMenu = Menu::where('slug', 'businesses')->firstOrFail();

        $allActiveMenus = Menu::where('is_active', 1)->orderBy('order')->get();
        $leafMenus = collect();

        $findLeaves = function ($parentId) use (&$findLeaves, $allActiveMenus, &$leafMenus) {
            $children = $allActiveMenus->where('parent_id', $parentId);

            foreach ($children as $child) {
                $hasChildren = $allActiveMenus->where('parent_id', $child->id)->isNotEmpty();

                if (!$hasChildren) {
                    $leafMenus->push($child);
                } else {
                    $findLeaves($child->id);
                }
            }
        };

        $findLeaves($parentMenu->id);

        return view('admin.concerns.index', compact('leafMenus', 'parentMenu'));
    }

    public function fetch(Menu $menu)
    {
        $concern = Concern::where('menu_id', $menu->id)->first() ?? new Concern(['menu_id' => $menu->id]);
        $galleries = $concern->exists ? $concern->galleries : collect();

        return response()->json([
            'html' => view('admin.concerns.partials.content', compact('menu', 'concern', 'galleries'))->render()
        ]);
    }

    public function updateInfo(Request $request, Menu $menu)
    {
        Concern::updateOrCreate(
            ['menu_id' => $menu->id],
            ['web_address' => $request->web_address]
        );
        return response()->json(['success' => true]);
    }

    public function updateRedirect(Request $request, Menu $menu)
    {
        Concern::updateOrCreate(
            ['menu_id' => $menu->id],
            ['is_redirect' => $request->is_redirect]
        );
        return response()->json(['success' => true]);
    }

    public function updateDescription(Request $request, Menu $menu)
    {
        Concern::updateOrCreate(
            ['menu_id' => $menu->id],
            ['description' => $request->description]
        );
        return response()->json(['success' => true]);
    }

    public function uploadCover(Request $request, Menu $menu)
    {
        $request->validate(['image' => 'required|image|mimes:jpg,jpeg,png,webp|max:102400']);

        try {
            $concern = Concern::firstOrCreate(['menu_id' => $menu->id]);

            if ($concern->cover_photo_path && Storage::disk('public')->exists($concern->cover_photo_path)) {
                Storage::disk('public')->delete($concern->cover_photo_path);
            }

            $fileName = time() . '.webp';
            $relativeDir = "concerns/{$menu->slug}/cover-photo";

            if (!Storage::disk('public')->exists($relativeDir)) {
                Storage::disk('public')->makeDirectory($relativeDir);
            }

            $fullPath = storage_path("app/public/{$relativeDir}/{$fileName}");
            $this->processConcernImage($request->file('image')->getRealPath(), $fullPath, 10 / 4);

            $concern->update(['cover_photo_path' => "{$relativeDir}/{$fileName}"]);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteCover(Menu $menu)
    {
        $concern = Concern::where('menu_id', $menu->id)->first();
        if ($concern && $concern->cover_photo_path && Storage::disk('public')->exists($concern->cover_photo_path)) {
            Storage::disk('public')->delete($concern->cover_photo_path);
            $concern->update(['cover_photo_path' => null]);
        }
        return response()->json(['success' => true]);
    }

    public function uploadGallery(Request $request, Menu $menu)
    {
        $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:102400'
        ]);

        try {
            $concern = Concern::firstOrCreate(['menu_id' => $menu->id]);
            $relativeDir = "concerns/{$menu->slug}/photo-gallery";

            if (!Storage::disk('public')->exists($relativeDir)) {
                Storage::disk('public')->makeDirectory($relativeDir);
            }

            $currentMaxOrder = $concern->galleries()->max('order') ?? 0;
            $baseTime = time();

            foreach ($request->file('photos') as $index => $file) {
                $fileName = ($baseTime + $index) . '.webp';
                $fullPath = storage_path("app/public/{$relativeDir}/{$fileName}");

                $this->processConcernImage($file->getRealPath(), $fullPath, 23 / 9);

                $concern->galleries()->create([
                    'file_path' => "{$relativeDir}/{$fileName}",
                    'order' => $currentMaxOrder + $index + 1
                ]);
            }

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function replaceGallery(Request $request, ConcernGallery $concernGallery)
    {
        $request->validate(['image' => 'required|image|mimes:jpg,jpeg,png,webp|max:102400']);

        try {
            if (Storage::disk('public')->exists($concernGallery->file_path)) {
                Storage::disk('public')->delete($concernGallery->file_path);
            }

            $fileName = time() . '.webp';
            $menuSlug = $concernGallery->concern->menu->slug;
            $relativeDir = "concerns/{$menuSlug}/photo-gallery";

            if (!Storage::disk('public')->exists($relativeDir)) {
                Storage::disk('public')->makeDirectory($relativeDir);
            }

            $fullPath = storage_path("app/public/{$relativeDir}/{$fileName}");
            $this->processConcernImage($request->file('image')->getRealPath(), $fullPath, 23 / 9);

            $concernGallery->update(['file_path' => "{$relativeDir}/{$fileName}"]);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteGallery(ConcernGallery $concernGallery)
    {
        if (Storage::disk('public')->exists($concernGallery->file_path)) {
            Storage::disk('public')->delete($concernGallery->file_path);
        }
        $concernGallery->delete();
        return response()->json(['success' => true]);
    }

    public function updateGalleryOrder(Request $request)
    {
        foreach ($request->orders as $item) {
            ConcernGallery::where('id', $item['id'])->update(['order' => $item['order']]);
        }
        return response()->json(['success' => true]);
    }

    private function processConcernImage($sourcePath, $destinationPath, $targetRatio)
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

    public function frontendShow(Menu $menu)
    {
        $concern = $menu->concern()->with('galleries')->first();

        if ($concern && $concern->is_redirect && !empty($concern->web_address)) {
            return redirect()->away($concern->web_address);
        }

        return view('businesses.show', ['pageMenu' => $menu, 'concern' => $concern]);
    }

    public function frontendPhotoGallery(Menu $menu)
    {
        $concerns = Concern::whereHas('galleries')
            ->with([
                'galleries' => function ($query) {
                    $query->orderBy('order', 'asc');
                },
                'menu'
            ])
            ->get()
            ->sortBy(function ($concern) {
                $menu = $concern->menu;

                $parentOrder = $menu->parent->order ?? 0;
                $grandParentOrder = $menu->parent->parent->order ?? 0;

                return sprintf(
                    '%03d-%03d-%03d',
                    $grandParentOrder,
                    $parentOrder,
                    $menu->order
                );
            });

        return view('program-and-events.photo-gallery', [
            'menu' => $menu,
            'pageMenu' => $menu,
            'concerns' => $concerns
        ]);
    }
}