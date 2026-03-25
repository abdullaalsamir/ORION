@php
    $allMenus = \App\Models\Menu::where('is_active', 1)
        ->orderBy('order')
        ->get();

    if (!function_exists('sortMenusByTree')) {
        function sortMenusByTree($menus, $parentId = null)
        {
            $branch = collect();
            foreach ($menus->where('parent_id', $parentId) as $menu) {
                $branch->push($menu);
                $children = sortMenusByTree($menus, $menu->id);
                $branch = $branch->merge($children);
            }
            return $branch;
        }
    }

    $multifunctionalMenus = sortMenusByTree($allMenus)->filter(function ($menu) {
        return $menu->is_multifunctional == 1 && $menu->isEffectivelyActive();
    });
@endphp

<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="turbo-cache-control" content="no-cache">
    <title>@yield('title', 'Admin Panel') | ORION</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>

<body class="h-full flex bg-slate-50 overflow-hidden font-sans text-base antialiased"
    data-admin-name="{{ auth('admin')->user()->name }}">

    <aside id="admin-sidebar" data-turbo-permanent class="w-70 bg-admin-blue text-white flex flex-col shrink-0 z-20">
        <div class="h-16 flex items-center justify-center border-b border-white/10 px-6">
            <h2 class="text-lg font-bold text-slate-200 tracking-tight mb-0">Admin Panel</h2>
        </div>

        <nav class="sidebar-nav flex-1 overflow-y-auto space-y-1 py-4 custom-scrollbar">
            @php
                $navItems = [
                    ['route' => 'admin.dashboard', 'pattern' => 'admin', 'icon' => 'fas fa-chart-line', 'label' => 'Dashboard'],
                    ['route' => 'admin.menus', 'pattern' => 'admin/menus*', 'icon' => 'fas fa-bars', 'label' => 'Menus'],
                    ['route' => 'admin.pages', 'pattern' => 'admin/pages*', 'icon' => 'fas fa-file-alt', 'label' => 'Pages'],
                    ['route' => 'admin.banners', 'pattern' => 'admin/banners*', 'icon' => 'fas fa-images', 'label' => 'Banners'],
                    ['route' => 'admin.sliders.index', 'pattern' => 'admin/sliders*', 'icon' => 'fas fa-film', 'label' => 'Swiper Slider'],
                ];
            @endphp

            @foreach($navItems as $item)
                <a href="{{ route($item['route']) }}"
                    class="flex items-center gap-3 px-4 py-3 transition-all border-l-4 {{ request()->is($item['pattern']) ? 'bg-white/10 text-accent border-accent' : 'text-slate-300 hover:bg-white/5 hover:text-white border-transparent' }}">
                    <i class="{{ $item['icon'] }} w-5 text-center"></i>
                    <span class="font-medium">{{ $item['label'] }}</span>
                </a>
            @endforeach

            @foreach($multifunctionalMenus as $mMenu)
                <a href="{{ url('admin/' . $mMenu->slug) }}"
                    class="flex items-center gap-3 px-4 py-3 transition-all border-l-4 {{ request()->is('admin/' . $mMenu->slug . '*') ? 'bg-white/10 text-accent border-accent' : 'text-slate-300 hover:bg-white/5 hover:text-white border-transparent' }}">
                    <i class="fas fa-th-large w-5 text-center"></i>
                    <span class="font-medium">{{ $mMenu->name }}</span>
                </a>
            @endforeach

            <a href="{{ route('admin.footer') }}"
                class="flex items-center gap-3 px-4 py-3 transition-all border-l-4 {{ request()->is('admin/footer*') ? 'bg-white/10 text-accent border-accent' : 'text-slate-300 hover:bg-white/5 hover:text-white border-transparent' }}">
                <i class="fas fa-socks w-5 text-center"></i>
                <span class="font-medium">Footer</span>
            </a>

            <a href="{{ route('admin.settings.index') }}"
                class="flex items-center gap-3 px-4 py-3 transition-all border-l-4 {{ request()->is('admin/settings*') ? 'bg-white/10 text-accent border-accent' : 'text-slate-300 hover:bg-white/5 hover:text-white border-transparent' }}">
                <i class="fas fa-cog w-5 text-center"></i>
                <span class="font-medium">Settings</span>
            </a>
        </nav>
    </aside>

    <div class="flex-1 flex flex-col min-w-0">
        <header id="admin-header" data-turbo-permanent
            class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8 shrink-0 z-10">
            @php
                $hr = now()->format('H');
                $greeting = $hr < 12 ? 'Good Morning' : ($hr < 17 ? 'Good Afternoon' : 'Good Evening');
            @endphp

            <div id="greetingText" class="text-base font-medium">
                <span class="text-slate-400 font-normal">{{ $greeting }}, </span>
                <span class="text-slate-600 font-semibold">{{ auth('admin')->user()->name }}</span>
            </div>

            <div class="hidden sm:flex items-center gap-2 bg-slate-50 px-4 py-1.5 rounded-full border border-slate-100">
                <i class="far fa-clock text-admin-blue text-sm"></i>
                <span id="clock"
                    class="text-sm font-mono font-bold text-slate-600 text-center tracking-widest uppercase">00:00:00
                    AM</span>
            </div>

            <div class="flex items-center gap-4 justify-end">
                <form action="{{ route('admin.logout') }}" method="POST" class="inline" data-turbo="false">
                    @csrf
                    <button type="submit"
                        class="h-8 w-10 flex items-center justify-center rounded-xl border border-slate-100 text-red-500 hover:bg-red-50 transition-colors cursor-pointer"
                        title="Logout">
                        <i class="fas fa-arrow-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </header>

        <main class="flex-1 overflow-hidden p-4">
            @yield('content')
        </main>
    </div>
</body>

</html>