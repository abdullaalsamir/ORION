<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    private function generateSlug($name)
    {
        return Str::of($name)
            ->replace('&', 'and')
            ->lower()
            ->slug('-');
    }

    public function index()
    {
        $homeMenu = Menu::firstOrCreate(
            ['slug' => 'home'],
            ['name' => 'Home', 'is_active' => 1, 'order' => -1]
        );

        $menus = Menu::whereNull('parent_id')
            ->where('slug', '!=', 'home')
            ->orderBy('order')
            ->with('children.children')
            ->get();

        return view('admin.menus.index', compact('menus', 'homeMenu'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'parent_id' => 'nullable|exists:menus,id',
            'is_multifunctional' => 'required|in:0,1'
        ]);

        $menu = Menu::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'is_multifunctional' => $request->is_multifunctional,
            'order' => Menu::where('parent_id', $request->parent_id)->max('order') + 1,
        ]);

        $menu->slug = $this->generateSlug($menu->name);
        $menu->save();

        return back()->with('success', 'Menu created successfully');
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name' => 'required|string',
            'parent_id' => 'nullable|exists:menus,id',
            'is_multifunctional' => 'required|in:0,1'
        ]);

        $parentId = $request->parent_id;
        $isActive = $request->has('is_active');

        $menu->update([
            'name' => $request->name,
            'parent_id' => $parentId,
            'is_active' => $isActive,
            'is_multifunctional' => $request->is_multifunctional,
        ]);

        $menu->slug = $this->generateSlug($menu->name);
        $menu->save();

        return back()->with('success', 'Menu updated successfully');
    }

    public function delete(Menu $menu)
    {
        $oldOrder = $menu->order;
        $parentId = $menu->parent_id;

        $children = $menu->children()->orderBy('order')->get();
        $childCount = $children->count();

        if ($childCount > 0) {
            Menu::where('parent_id', $parentId)
                ->where('order', '>', $oldOrder)
                ->increment('order', $childCount);

            foreach ($children as $index => $child) {
                $child->update([
                    'parent_id' => $parentId,
                    'order' => $oldOrder + $index
                ]);
            }
        }

        $menu->delete();

        return response()->json(['success' => true, 'message' => 'Menu deleted and hierarchy reorganized.']);
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'menus' => 'required|array',
            'menus.*.id' => 'required|exists:menus,id',
            'menus.*.parent_id' => 'nullable|exists:menus,id',
            'menus.*.sort_order' => 'required|integer|min:0'
        ]);

        Menu::unguard();

        foreach ($request->menus as $item) {
            Menu::where('id', $item['id'])->update([
                'parent_id' => $item['parent_id'] ?? null,
                'order' => $item['sort_order']
            ]);
        }

        Menu::reguard();

        return response()->json(['success' => true]);
    }

    public function updatePage(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'content' => 'nullable|string'
        ]);

        $menu->update([
            'content' => $validated['content']
        ]);

        return response()->json(['success' => true]);
    }

    public function pages()
    {
        $homeMenu = Menu::firstOrCreate(
            ['slug' => 'home'],
            ['name' => 'Home', 'is_active' => 1, 'order' => -1]
        );

        $menus = Menu::whereNull('parent_id')
            ->where('slug', '!=', 'home')
            ->orderBy('order')
            ->with('children.children')
            ->get();

        return view('admin.pages.index', compact('menus', 'homeMenu'));
    }

    public function showMultifunctional($slug)
    {
        $menu = Menu::where('slug', $slug)
            ->where('is_multifunctional', 1)
            ->first();

        abort_if(!$menu, 404);

        if ($menu->slug === 'csr') {
            return (new CsrController)->index();
        }

        if ($menu->slug === 'businesses') {
            return (new ConcernController)->index();
        }

        if ($menu->slug === 'news-and-announcements') {
            return (new NewsController)->index();
        }

        if ($menu->slug === 'video-gallery') {
            return (new VideoGalleryController)->index();
        }

        if ($menu->slug === 'board-of-directors') {
            return (new BoardDirectorController)->index();
        }

        if ($menu->slug === 'connect') {
            return (new ConnectController)->index();
        }

        if (view()->exists("admin.{$slug}.index")) {
            $controllerName = 'App\\Http\\Controllers\\Admin\\' . Str::studly($slug) . 'Controller';
            if (class_exists($controllerName)) {
                return (new $controllerName)->index();
            }
        }

        return view('admin.multifunctional.placeholder', compact('menu'));
    }
}
