<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Banner;
use App\Models\Slider;
use App\Models\Generic;
use App\Models\Product;
use App\Models\CsrItem;
use App\Models\Scholarship;
use App\Models\MedicalJournal;
use App\Models\BoardDirector;
use App\Models\AnnualReports;
use App\Models\QuarterlyReports;
use App\Models\HalfYearlyReports;
use App\Models\PriceSensitiveInformation;
use App\Models\NewsItem;
use App\Models\Connect;
use App\Models\Career;
use Carbon\Carbon;

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

        $reportStat = function ($class, $dateCol) use ($baseStat) {
            $data = $class::get([$dateCol, 'is_active']);
            $stat = $baseStat($data);
            $stat['years'] = $data->pluck($dateCol)->map(function ($d) {
                return Carbon::parse($d)->format('Y');
            })->unique()->count();
            return $stat;
        };

        $mjData = MedicalJournal::get(['year', 'is_active']);
        $mjStat = $baseStat($mjData);
        $mjStat['years'] = $mjData->pluck('year')->unique()->count();

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
                'title' => 'Generics',
                'icon' => 'fa-vials',
                ...$baseStat(Generic::get(['is_active'])),
            ],
            [
                'title' => 'Products',
                'icon' => 'fa-pills',
                ...$baseStat(Product::get(['is_active'])),
            ],
            [
                'title' => 'CSR List',
                'icon' => 'fa-hand-holding-heart',
                ...$baseStat(CsrItem::get(['is_active'])),
            ],
            [
                'title' => 'Scholarships',
                'icon' => 'fa-user-graduate',
                ...$baseStat(Scholarship::get(['is_active'])),
            ],
            [
                'title' => 'Medical Journals',
                'icon' => 'fa-book-medical',
                ...$mjStat,
                'subs' => [['label' => 'Journal Years', 'value' => $mjStat['years']]]
            ],
            [
                'title' => 'Board Directors',
                'icon' => 'fa-user-tie',
                ...$baseStat(BoardDirector::get(['is_active'])),
            ],
            [
                'title' => 'Annual Reports',
                'icon' => 'fa-file-invoice-dollar',
                ...($ar = $reportStat(AnnualReports::class, 'publication_date')),
                'subs' => [['label' => 'Annual Years', 'value' => $ar['years']]]
            ],
            [
                'title' => 'Quarterly Reports',
                'icon' => 'fa-chart-pie',
                ...($qr = $reportStat(QuarterlyReports::class, 'publication_date')),
                'subs' => [['label' => 'Quarterly Years', 'value' => $qr['years']]]
            ],
            [
                'title' => 'Half Yearly Reports',
                'icon' => 'fa-chart-bar',
                ...($hyr = $reportStat(HalfYearlyReports::class, 'publication_date')),
                'subs' => [['label' => 'Half Yearly Years', 'value' => $hyr['years']]]
            ],
            [
                'title' => 'Price Sensitive Info',
                'icon' => 'fa-info-circle',
                ...($psi = $reportStat(PriceSensitiveInformation::class, 'publication_date')),
                'subs' => [['label' => 'Information Years', 'value' => $psi['years']]]
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