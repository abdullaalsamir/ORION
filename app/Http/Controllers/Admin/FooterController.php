<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Footer;
use App\Models\Menu;
use Illuminate\Http\Request;

class FooterController extends Controller
{
    public function index()
    {
        $footer = Footer::firstOrCreate(['id' => 1], [
            'company' => '',
            'quick_links' => [],
            'social_links' => [
                ['platform' => 'Facebook', 'url' => '', 'icon' => 'fa-facebook-f'],
                ['platform' => 'LinkedIn', 'url' => '', 'icon' => 'fa-linkedin-in'],
                ['platform' => 'YouTube', 'url' => '', 'icon' => 'fa-youtube'],
                ['platform' => 'Website', 'url' => '', 'icon' => 'fa-globe'],
            ]
        ]);

        $menus = Menu::getFunctionalLeafMenus();
        return view('admin.footer.index', compact('footer', 'menus'));
    }

    public function update(Request $request)
    {
        $footer = Footer::firstOrCreate(['id' => 1]);

        $data = $request->except([
            'ql_menu_id',
            'social_platform',
            'social_url',
            'social_icon'
        ]);

        if ($request->has('ql_menu_id')) {
            $ql = [];

            foreach ($request->ql_menu_id as $menuId) {
                $ql[] = [
                    'menu_id' => $menuId ?: null
                ];
            }

            $footer->quick_links = $ql;
        }

        if ($request->has('social_platform')) {
            $sl = [];

            foreach ($request->social_platform as $index => $platform) {
                $sl[] = [
                    'platform' => $platform,
                    'url' => $request->social_url[$index] ?? '',
                    'icon' => $request->social_icon[$index] ?? '',
                ];
            }

            $footer->social_links = $sl;
        }

        $footer->fill($data);
        $footer->save();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Footer updated successfully');
    }
}