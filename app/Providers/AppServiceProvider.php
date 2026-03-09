<?php

namespace App\Providers;

use App\Models\Menu;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {

    }

    public function boot()
    {
        Paginator::useTailwind();

        View::composer([
            'layouts.*',
            'pages.*',
            'partials.*'
        ], function ($view) {

            $menus = Menu::whereNull('parent_id')
                ->where('is_active', true)
                ->orderBy('order')
                ->with([
                    'children' => function ($q) {
                        $q->where('is_active', true)
                            ->orderBy('order')
                            ->with([
                                'children' => function ($q) {
                                    $q->where('is_active', true)
                                        ->orderBy('order');
                                }
                            ]);
                    }
                ])->get();

            $view->with('menus', $menus);
        });
    }
}
