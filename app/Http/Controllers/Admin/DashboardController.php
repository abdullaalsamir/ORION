<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Banner;
use App\Models\Slider;
use App\Models\CsrItem;
use App\Models\BoardDirector;
use App\Models\NewsItem;
use App\Models\Connect;
use App\Models\Career;

class DashboardController extends Controller
{
    public function index()
    {
        $allMenus = Menu::get(['id', 'parent_id', 'is_active', 'is_multifunctional']);
        $activeMenus = $allMenus->where('is_active', 1);
        $parentIds = $allMenus->pluck('parent_id')->filter();
        $leafPages = $allMenus->whereNotIn('id', $parentIds);

        $rootIds = $activeMenus->whereNull('parent_id')->pluck('id');
        $subIds = $activeMenus->whereIn('parent_id', $rootIds)->pluck('id');
        $subSubCount = $activeMenus->whereIn('parent_id', $subIds)->count();

        $staticPages = $leafPages->where('is_multifunctional', 0)->where('is_active', 1);
        $multiPages = $leafPages->where('is_multifunctional', 1)->where('is_active', 1);

        $allBanners = Banner::get(['id', 'menu_id', 'is_active']);
        $bannersOnStatic = $allBanners->where('is_active', 1)->whereIn('menu_id', $staticPages->pluck('id'))->count();

        $allNews = NewsItem::get(['file_type', 'is_active']);
        $newsImages = $allNews->where('is_active', 1)->where('file_type', 'image')->count();
        $newsPdfs = $allNews->where('is_active', 1)->where('file_type', 'pdf')->count();

        $baseStat = function ($data) {
            return [
                'total' => $data->count(),
                'active' => $data->where('is_active', 1)->count(),
                'inactive' => $data->where('is_active', 0)->count(),
            ];
        };

        $cards = [
            [
                'title' => 'Menus',
                'icon' => 'fa-sitemap',
                ...$baseStat($allMenus),
                'subs' => [
                    ['label' => 'Menu', 'value' => $rootIds->count()],
                    ['label' => 'SubMenu', 'value' => $subIds->count()],
                    ['label' => '3rd Level', 'value' => $subSubCount],
                ]
            ],
            [
                'title' => 'Pages',
                'icon' => 'fa-file-alt',
                ...$baseStat($leafPages),
                'subs' => [
                    ['label' => 'Static', 'value' => $staticPages->count()],
                    ['label' => 'Multifunctional', 'value' => $multiPages->count()],
                ]
            ],
            [
                'title' => 'Banners',
                'icon' => 'fa-images',
                ...$baseStat($allBanners),
                'subs' => [['label' => 'On Static Pages', 'value' => $bannersOnStatic]]
            ],
            [
                'title' => 'Swiper Sliders',
                'icon' => 'fa-film',
                ...$baseStat(Slider::get(['is_active'])),
            ],
            [
                'title' => 'CSR List',
                'icon' => 'fa-hand-holding-heart',
                ...$baseStat(CsrItem::get(['is_active'])),
            ],
            [
                'title' => 'Board Directors',
                'icon' => 'fa-user-tie',
                ...$baseStat(BoardDirector::get(['is_active'])),
            ],
            [
                'title' => 'News & Announces',
                'icon' => 'fa-newspaper',
                ...$baseStat($allNews),
                'subs' => [
                    ['label' => 'News', 'value' => $newsImages],
                    ['label' => 'Announce', 'value' => $newsPdfs],
                ]
            ],
            [
                'title' => 'Product Complaints',
                'icon' => 'fa-exclamation-triangle',
                'total' => Connect::count(),
                'no_status' => true,
            ],
            [
                'title' => 'Job Openings',
                'icon' => 'fa-briefcase',
                ...$baseStat(Career::get(['is_active'])),
            ],
        ];

        return view('admin.dashboard.index', compact('cards'));
    }
}