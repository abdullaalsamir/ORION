<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Slider;
use App\Models\CsrItem;
use App\Models\NewsItem;
use App\Models\Concern;
use App\Http\Controllers\Admin\CsrController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\ConcernController;
use App\Http\Controllers\Admin\VideoGalleryController;
use App\Http\Controllers\Admin\BoardDirectorController;
use App\Http\Controllers\Admin\LeadershipController;
use App\Http\Controllers\Admin\ConnectController;

class PageController extends Controller
{
    public function home()
    {
        $menu = Menu::where('slug', 'home')->first();
        $sliders = Slider::where('is_active', 1)->orderBy('order')->get();
        $csrItems = CsrItem::where('is_active', 1)->latest('csr_date')->take(6)->get();
        $csrMenu = Menu::where('slug', 'csr')->first();
        $newsMenu = Menu::where('slug', 'news-and-announcements')->first();

        $pinnedNews = NewsItem::where('is_active', 1)
            ->where('is_pin', 1)->latest('news_date')->first();

        $homeNews = NewsItem::where('is_active', 1)
            ->latest('news_date')->take(10)->get();

        $homeConcerns = Concern::whereNotNull('cover_photo_path')
            ->whereHas('menu', function ($q) {
                $q->where('is_active', 1);
            })
            ->with('menu')
            ->get()
            ->sortBy(function ($concern) {
                $menu = $concern->menu;
                $orderPath = [];
                $current = $menu;
                while ($current) {
                    array_unshift($orderPath, sprintf('%04d', $current->order));
                    $current = $current->parent;
                }
                return implode('-', $orderPath);
            })->values();

        return view('layouts.app', compact(
            'menu',
            'sliders',
            'csrItems',
            'homeConcerns',
            'csrMenu',
            'pinnedNews',
            'homeNews',
            'newsMenu'
        ));
    }

    public function page(string $slug)
    {
        if ($slug === 'home')
            return redirect('/', 301);

        $menu = Menu::all()->first(fn($m) => $m->full_slug === $slug);

        if ($menu) {
            abort_if(!$menu->isEffectivelyActive(), 404);

            if ($menu->is_multifunctional && $menu->slug === 'csr')
                return (new CsrController)->frontendIndex($menu);
            if ($menu->is_multifunctional && $menu->slug === 'news-and-announcements')
                return (new NewsController)->frontendIndex($menu);
            if ($menu->slug === 'board-of-directors')
                return (new BoardDirectorController)->frontendIndex($menu);
            if ($menu->slug === 'leadership')
                return (new LeadershipController)->frontendIndex($menu);
            if ($menu->is_multifunctional && $menu->slug === 'video-gallery')
                return (new VideoGalleryController)->frontendIndex($menu);
            if ($menu->slug === 'connect')
                return (new ConnectController)->frontendIndex($menu);

            $rootParent = $menu;
            while ($rootParent->parent_id) {
                $rootParent = $rootParent->parent;
            }
            if ($rootParent->slug === 'businesses')
                return (new ConcernController)->frontendShow($menu);
            if ($menu->slug === 'photo-gallery')
                return (new ConcernController)->frontendPhotoGallery($menu);

            abort_if($menu->children()->exists(), 404);
            return view('layouts.app', compact('menu'));
        }

        $segments = explode('/', $slug);
        $itemSlug = array_pop($segments);
        $parentPath = implode('/', $segments);

        $parentMenu = Menu::all()->first(fn($m) => $m->full_slug === $parentPath);

        if ($parentMenu && $parentMenu->is_multifunctional) {
            if ($parentMenu->slug === 'csr')
                return (new CsrController)->frontendShow($parentMenu, $itemSlug);
            if ($parentMenu->slug === 'news-and-announcements')
                return (new NewsController)->frontendShow($parentMenu, $itemSlug);
            if ($parentMenu->slug === 'board-of-directors')
                return (new BoardDirectorController)->frontendShow($parentMenu, $itemSlug);
            if ($parentMenu->slug === 'leadership')
                return (new LeadershipController)->frontendShow($parentMenu, $itemSlug);
        }

        abort(404);
    }

    public function sitemap()
    {
        $menus = Menu::with([
            'children' => function ($q) {
                $q->where('is_active', 1)->orderBy('order');
            },
            'children.children' => function ($q) {
                $q->where('is_active', 1)->orderBy('order');
            }
        ])
            ->whereNull('parent_id')->where('is_active', 1)->where('slug', '!=', 'home')->orderBy('order')->get();

        $menu = (object) ['name' => 'Sitemap', 'content' => null];

        return view('partials.sitemap', compact('menus', 'menu'));
    }
}