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

            if ($concern->cover_photo_path) {
                $oldPath = $concern->cover_photo_path;
                $oldBase = basename($oldPath, '.webp');
                $oldThumbDir = dirname($oldPath) . '/thumbs';

                Storage::disk('public')->delete([
                    $oldPath,
                    "{$oldThumbDir}/{$oldBase}-200.webp",
                    "{$oldThumbDir}/{$oldBase}-700.webp"
                ]);
            }

            $baseName = time();
            $fileName = $baseName . '.webp';
            $relativeDir = "concerns/{$menu->slug}/cover-photo";
            $thumbDir = "{$relativeDir}/thumbs";

            if (!Storage::disk('public')->exists($thumbDir)) {
                Storage::disk('public')->makeDirectory($thumbDir);
            }

            $mainPath = storage_path("app/public/{$relativeDir}/{$fileName}");
            $thumb200Path = storage_path("app/public/{$thumbDir}/{$baseName}-200.webp");
            $thumb700Path = storage_path("app/public/{$thumbDir}/{$baseName}-700.webp");

            $this->processConcernImage($request->file('image')->getRealPath(), $mainPath, 20 / 9, 1500);
            $this->processConcernImage($request->file('image')->getRealPath(), $thumb200Path, 20 / 9, 200);
            $this->processConcernImage($request->file('image')->getRealPath(), $thumb700Path, 20 / 9, 700);

            $concern->update(['cover_photo_path' => "{$relativeDir}/{$fileName}"]);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteCover(Menu $menu)
    {
        $concern = Concern::where('menu_id', $menu->id)->first();
        if ($concern && $concern->cover_photo_path) {
            $oldPath = $concern->cover_photo_path;
            $oldBase = basename($oldPath, '.webp');
            $oldThumbDir = dirname($oldPath) . '/thumbs';

            Storage::disk('public')->delete([
                $oldPath,
                "{$oldThumbDir}/{$oldBase}-200.webp",
                "{$oldThumbDir}/{$oldBase}-700.webp"
            ]);

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
            $sliderDir = "concerns/{$menu->slug}/concern-slider";
            $thumbDir = "{$relativeDir}/thumbs";

            if (!Storage::disk('public')->exists($thumbDir))
                Storage::disk('public')->makeDirectory($thumbDir);
            if (!Storage::disk('public')->exists($sliderDir))
                Storage::disk('public')->makeDirectory($sliderDir);

            $currentMaxOrder = $concern->galleries()->max('order') ?? 0;
            $baseTime = time();

            foreach ($request->file('photos') as $index => $file) {
                $baseName = $baseTime + $index;
                $fileName = $baseName . '.webp';

                $mainPath = storage_path("app/public/{$relativeDir}/{$fileName}");
                $sliderPath = storage_path("app/public/{$sliderDir}/{$fileName}");
                $thumb250Path = storage_path("app/public/{$thumbDir}/{$baseName}-250.webp");
                $thumb350Path = storage_path("app/public/{$thumbDir}/{$baseName}-350.webp");
                $thumb700Path = storage_path("app/public/{$thumbDir}/{$baseName}-700.webp");

                $this->processConcernImage($file->getRealPath(), $mainPath, 20 / 9, 2000);
                $this->processConcernImage($file->getRealPath(), $sliderPath, 23 / 9, 2000);
                $this->processConcernImage($file->getRealPath(), $thumb250Path, 20 / 9, 250);
                $this->processConcernImage($file->getRealPath(), $thumb350Path, 20 / 9, 350);
                $this->processConcernImage($file->getRealPath(), $thumb700Path, 20 / 9, 700);

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
            $oldPath = $concernGallery->file_path;
            $oldBase = basename($oldPath, '.webp');
            $menuSlug = $concernGallery->concern->menu->slug;

            $oldDir = dirname($oldPath);
            $oldThumbDir = "{$oldDir}/thumbs";
            $oldSliderDir = "concerns/{$menuSlug}/concern-slider";

            Storage::disk('public')->delete([
                $oldPath,
                "{$oldSliderDir}/{$oldBase}.webp",
                "{$oldThumbDir}/{$oldBase}-250.webp",
                "{$oldThumbDir}/{$oldBase}-350.webp",
                "{$oldThumbDir}/{$oldBase}-700.webp"
            ]);

            $baseName = time();
            $fileName = $baseName . '.webp';

            $relativeDir = "concerns/{$menuSlug}/photo-gallery";
            $sliderDir = "concerns/{$menuSlug}/concern-slider";
            $thumbDir = "{$relativeDir}/thumbs";

            if (!Storage::disk('public')->exists($thumbDir))
                Storage::disk('public')->makeDirectory($thumbDir);
            if (!Storage::disk('public')->exists($sliderDir))
                Storage::disk('public')->makeDirectory($sliderDir);

            $mainPath = storage_path("app/public/{$relativeDir}/{$fileName}");
            $sliderPath = storage_path("app/public/{$sliderDir}/{$fileName}");
            $thumb250Path = storage_path("app/public/{$thumbDir}/{$baseName}-250.webp");
            $thumb350Path = storage_path("app/public/{$thumbDir}/{$baseName}-350.webp");
            $thumb700Path = storage_path("app/public/{$thumbDir}/{$baseName}-700.webp");

            $this->processConcernImage($request->file('image')->getRealPath(), $mainPath, 20 / 9, 2000);
            $this->processConcernImage($request->file('image')->getRealPath(), $sliderPath, 23 / 9, 2000);
            $this->processConcernImage($request->file('image')->getRealPath(), $thumb250Path, 20 / 9, 250);
            $this->processConcernImage($request->file('image')->getRealPath(), $thumb350Path, 20 / 9, 350);
            $this->processConcernImage($request->file('image')->getRealPath(), $thumb700Path, 20 / 9, 700);

            $concernGallery->update(['file_path' => "{$relativeDir}/{$fileName}"]);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteGallery(ConcernGallery $concernGallery)
    {
        $oldPath = $concernGallery->file_path;
        $oldBase = basename($oldPath, '.webp');
        $menuSlug = $concernGallery->concern->menu->slug;

        $oldDir = dirname($oldPath);
        $oldThumbDir = "{$oldDir}/thumbs";
        $oldSliderDir = "concerns/{$menuSlug}/concern-slider";

        Storage::disk('public')->delete([
            $oldPath,
            "{$oldSliderDir}/{$oldBase}.webp",
            "{$oldThumbDir}/{$oldBase}-250.webp",
            "{$oldThumbDir}/{$oldBase}-350.webp",
            "{$oldThumbDir}/{$oldBase}-700.webp"
        ]);

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

    private function processConcernImage($sourcePath, $destinationPath, $targetRatio, $maxWidth = 1500)
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
        if ($finalWidth > $maxWidth) {
            $finalWidth = $maxWidth;
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