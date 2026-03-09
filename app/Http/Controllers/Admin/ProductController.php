<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Generic;
use App\Models\Product;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;

class ProductController extends Controller
{
    public function index()
    {
        $menu = Menu::where('slug', 'products')->firstOrFail();
        $generics = Generic::orderBy('name')->get();
        $archivedCount = Product::whereNull('generic_id')->count();
        return view('admin.products.index', compact('menu', 'generics', 'archivedCount'));
    }

    public function fetchProducts($id)
    {
        if ($id == 0) {
            $products = Product::whereNull('generic_id')->orderBy('trade_name')->get();
            $generic = null;
        } else {
            $generic = Generic::findOrFail($id);
            $products = $generic->products()->orderBy('trade_name')->get();
        }
        return response()->json([
            'html' => view('admin.products.partials.product-list', compact('products', 'generic'))->render()
        ]);
    }

    public function storeGeneric(Request $request)
    {
        $validator = \Validator::make($request->all(), ['name' => 'required|unique:generics,name']);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()->first()], 422);
        }

        Generic::create(['name' => $request->name, 'is_active' => 1]);
        return response()->json(['success' => true]);
    }

    public function updateGeneric(Request $request, Generic $generic)
    {
        $validator = \Validator::make($request->all(), ['name' => 'required|unique:generics,name,' . $generic->id]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()->first()], 422);
        }

        $oldSlug = $generic->slug;
        $newSlug = Generic::generateSlug($request->name);

        if ($oldSlug !== $newSlug) {
            $oldPath = "products/{$oldSlug}";
            $newPath = "products/{$newSlug}";

            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->move($oldPath, $newPath);
            }

            $generic->name = $request->name;
            $generic->save();

            foreach ($generic->products as $product) {
                $product->image_path = str_replace($oldPath, $newPath, $product->image_path);
                $product->save();
            }
        }

        $generic->update(['is_active' => $request->boolean('is_active')]);
        return response()->json(['success' => true]);
    }

    public function deleteGeneric(Generic $generic)
    {
        try {
            $products = Product::where('generic_id', $generic->id)->get();

            foreach ($products as $product) {
                $oldPath = $product->image_path;
                $fileName = basename($oldPath);
                $newPath = "products/{$fileName}";

                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->move($oldPath, $newPath);
                }

                Product::where('id', $product->id)->update([
                    'generic_id' => null,
                    'image_path' => $newPath
                ]);
            }

            $generic->delete();
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function storeProduct(Request $request, Generic $generic)
    {
        $request->validate([
            'trade_name' => 'required',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:102400'
        ]);

        if (Product::where('generic_id', $generic->id)->where('trade_name', $request->trade_name)->exists()) {
            return response()->json(['success' => false, 'error' => "Product already exists in this generic."], 422);
        }

        try {
            $file = $request->file('image');

            $slug = Product::generateUniqueSlug($request->trade_name);
            $fileName = $slug . '.webp';
            $dir = "products/{$generic->slug}";
            $path = "{$dir}/{$fileName}";

            if (!Storage::disk('public')->exists($dir)) {
                Storage::disk('public')->makeDirectory($dir);
            }

            $this->processProductImage($file->getRealPath(), storage_path("app/public/{$path}"));

            $product = Product::create([
                'trade_name' => $request->trade_name,
                'slug' => $slug,
                'generic_id' => $generic->id,
                'image_path' => $path,
                'is_active' => 1,
            ] + $request->except(['image', '_token']));

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function updateProduct(Request $request, Product $product)
    {
        $request->validate(['trade_name' => 'required']);

        $newGenericId = $request->filled('generic_id')
            ? $request->input('generic_id')
            : $product->generic_id;
        $newTradeName = $request->trade_name;
        $product->trade_name = $newTradeName;
        $product->generic_id = $newGenericId;
        $product->is_active = $request->has('is_active') ? 1 : 0;
        $product->save();

        $product->refresh();

        $newFileName = $product->slug . '.webp';


        if ($newGenericId) {
            $exists = Product::where('generic_id', $newGenericId)->where('trade_name', $newTradeName)->where('id', '!=', $product->id)->exists();
            if ($exists)
                return response()->json(['success' => false, 'error' => "Product name exists in target generic."], 422);
        }

        try {
            $oldPath = $product->image_path;

            $newGeneric = $newGenericId ? Generic::find($newGenericId) : null;
            $newDir = $newGeneric ? "products/{$newGeneric->slug}" : "products";
            $newPath = "{$newDir}/{$newFileName}";

            if ($request->hasFile('image')) {
                Storage::disk('public')->delete($oldPath);
                $this->processProductImage($request->file('image')->getRealPath(), storage_path("app/public/{$newPath}"));
            } elseif ($oldPath !== $newPath) {
                if (Storage::disk('public')->exists($oldPath)) {
                    if (!Storage::disk('public')->exists($newDir))
                        Storage::disk('public')->makeDirectory($newDir);
                    Storage::disk('public')->move($oldPath, $newPath);
                }
            }

            $product->update([
                'trade_name' => $newTradeName,
                'generic_id' => $newGenericId,
                'image_path' => $newPath,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ] + $request->except([
                            'image',
                            '_method',
                            'is_active',
                            'generic_id',
                            'trade_name'
                        ]));

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteProduct(Product $product)
    {
        Storage::disk('public')->delete($product->image_path);
        $product->delete();
        return response()->json(['success' => true]);
    }

    private function processProductImage($sourcePath, $destinationPath)
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

    public function serveProductImage($genericSlug, $filename)
    {
        $storagePath = storage_path("app/public/products/{$genericSlug}/{$filename}");

        abort_if(!file_exists($storagePath), 404);

        return response()->file($storagePath);
    }

    public function frontendIndex($menu)
    {
        $products = Product::where('is_active', 1)
            ->whereHas('generic', fn($q) => $q->where('is_active', 1))
            ->with('generic')
            ->paginate(9);

        return view('products.index', compact('products', 'menu'));
    }

    public function frontendShow($generic_slug, $product_slug, $menu = null)
    {
        if (!$menu) {
            $menu = Menu::where('slug', 'products')->first();
        }

        $product = Product::whereHas(
            'generic',
            fn($q) => $q->where('slug', $generic_slug)
        )->where('slug', $product_slug)->firstOrFail();

        return view('products.show', compact('product', 'menu'));
    }
}