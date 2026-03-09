@extends('admin.layouts.app')

@section('title', 'Footer Management')

@php
    $nestedMenus = \App\Models\Menu::with(['children' => function($q) {
            $q->orderBy('order');
        }, 'children.children' => function($q) {
            $q->orderBy('order');
        }])
        ->whereNull('parent_id')
        ->where('slug', '!=', 'home')
        ->orderBy('order')
        ->get();

    $allActiveMenus = \App\Models\Menu::where('is_active', 1)->get();
@endphp

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="flex flex-col">
                <h1>Footer Management</h1>
                <p class="text-xs text-slate-400">
                    Configure site-wide contact information and social branding
                </p>
            </div>
        </div>

        <div class="admin-card-body bg-slate-50/20 custom-scrollbar">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <div class="bg-white rounded-2xl border border-slate-200 flex flex-col">
                    <div class="px-5 py-2 border-b border-slate-100 flex justify-between items-center">
                        <h1 class="text-lg!">Contact Us</h1>
                        <button onclick="openFooterModal('contactModal')" class="btn-icon">
                            <i class="fas fa-pencil text-xs"></i>
                        </button>
                    </div>

                    <div class="p-6 space-y-6">
                        <div>
                            <label class="text-[10px] font-black text-admin-blue uppercase tracking-widest mb-1 block">
                                Company Name
                            </label>
                            <div class="text-xs text-slate-500 font-medium space-y-1">
                                {{ $footer->company ?: '' }}
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] font-black text-admin-blue uppercase tracking-widest mb-1 block">
                                Address
                            </label>
                            <div class="text-xs text-slate-500 font-medium space-y-1">
                                <p>{{ $footer->address_1 ?: '' }}</p>
                                <p>{{ $footer->address_2 ?: '' }}</p>
                                <p>{{ $footer->address_3 ?: '' }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] font-black text-admin-blue uppercase tracking-widest mb-1 block">
                                Phone
                            </label>
                            <div class="text-xs text-slate-500 font-medium space-y-1">
                                <p>{{ $footer->phone_1 ?: '' }}</p>
                                <p>{{ $footer->phone_2 ?: '' }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] font-black text-admin-blue uppercase tracking-widest mb-1 block">
                                Fax
                            </label>
                            <p class="text-xs text-slate-500 font-medium">{{ $footer->fax ?: '' }}</p>
                        </div>

                        <div>
                            <label class="text-[10px] font-black text-admin-blue uppercase tracking-widest mb-1 block">
                                Email
                            </label>
                            <p class="text-xs text-slate-500 font-medium">{{ $footer->email ?: '' }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden flex flex-col">
                    <div class="px-5 py-2 border-b border-slate-100 flex justify-between items-center bg-white sticky top-0 z-10">
                        <h1 class="text-lg!">Map Location</h1>
                        <button onclick="openFooterModal('mapModal')" class="btn-icon">
                            <i class="fas fa-pencil text-xs"></i>
                        </button>
                    </div>

                    <div class="p-4 flex-1">
                        <div class="w-full h-full min-h-75 rounded-2xl overflow-hidden border border-slate-200 bg-slate-50 relative">
                            @if ($footer->map_url)
                                <iframe src="{{ $footer->map_url }}" class="w-full h-full" style="border:0;"></iframe>
                            @else
                                <div class="absolute inset-0 flex items-center justify-center text-slate-300 text-xs">
                                    No Map URL Configured
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden flex flex-col">
                    <div class="px-5 py-2 border-b border-slate-100 flex justify-between items-center bg-white sticky top-0 z-10">
                        <h1 class="text-lg!">Quick Links Slots</h1>
                        <button onclick="openFooterModal('qlModal')" class="btn-icon">
                            <i class="fas fa-pencil text-xs"></i>
                        </button>
                    </div>

                    <div class="p-6 space-y-2">
                        @for ($i = 0; $i < 8; $i++)
                            @php
                                $link = $footer->quick_links[$i] ?? null;
                                $m = null;
                                $isSpecial = false;
                                
                                if ($link && !empty($link['menu_id'])) {
                                    if ($link['menu_id'] === 'sitemap') {
                                        $m = (object)['name' => 'Sitemap'];
                                        $isSpecial = true;
                                    } elseif ($link['menu_id'] === 'career') {
                                        $m = (object)['name' => 'Career'];
                                        $isSpecial = true;
                                    } else {
                                        $m = $allActiveMenus->firstWhere('id', $link['menu_id']);
                                    }
                                }
                            @endphp

                            <div class="flex items-center justify-between p-3.5 rounded-2xl border {{ $m ? 'border-emerald-100 bg-emerald-50/40' : 'border-slate-100 bg-slate-50/50' }} transition-all">
                                <div class="flex items-center gap-4">
                                    <span class="text-[10px] font-black text-slate-300">{{ $i + 1 }}</span>
                                    <span class="text-xs font-bold {{ $m ? ($isSpecial ? 'text-orion-blue' : 'text-emerald-700') : 'text-slate-300' }}">
                                        {{ $m ? $m->name : '' }}
                                    </span>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 flex flex-col">
                    <div class="px-5 py-2 border-b border-slate-100 flex justify-between items-center">
                        <h1 class="text-lg!">Social Links</h1>
                        <button onclick="openFooterModal('followModal')" class="btn-icon">
                            <i class="fas fa-pencil text-xs"></i>
                        </button>
                    </div>

                    <div class="p-6 space-y-6">
                        <div>
                            <label class="text-[10px] font-black text-slate-500 uppercase mb-2 block">
                                Description
                            </label>
                            <p class="text-xs text-slate-500">
                                {{ $footer->follow_us_desc ?: '' }}
                            </p>
                        </div>

                        <div class="space-y-3">
                            @foreach ($footer->social_links ?? [] as $social)
                                <div class="flex items-center gap-4 p-3 rounded-2xl border border-slate-100 bg-white">
                                    <div class="w-8 h-8 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400">
                                        <i class="{{ $social['icon'] === 'fa-globe' ? 'fas' : 'fab' }} {{ $social['icon'] }} text-xs"></i>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="text-[9px] font-black text-slate-300 uppercase tracking-tighter">
                                            {{ $social['platform'] }}
                                        </div>
                                        <div class="text-[11px] font-bold truncate {{ $social['url'] ? 'text-admin-blue' : 'text-red-200' }}">
                                            {{ $social['url'] ?: 'No link set' }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('admin.footer.partials.modals')
@endsection