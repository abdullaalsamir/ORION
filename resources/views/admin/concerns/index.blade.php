@extends('admin.layouts.app')
@section('title', 'Concerns Management')

@section('content')
    <div class="flex gap-6 h-full overflow-hidden">
        <aside class="w-80 bg-white rounded-2xl border border-slate-200 flex flex-col overflow-hidden">
            <div class="admin-card-header">
                <div class="flex flex-col">
                    <h1>Concerns</h1>
                    <p class="text-xs text-slate-400">Select a concern to manage it</p>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-2 custom-scrollbar bg-slate-50/30">
                @foreach($leafMenus as $menu)
                    <div class="leaf-menu-item p-3.5 bg-white border border-slate-200 rounded-2xl hover:border-admin-blue/50 [&.active]:border-admin-blue/50 cursor-pointer transition-all group"
                        data-id="{{ $menu->id }}" onclick="loadConcern({{ $menu->id }}, this)">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 group-hover:text-admin-blue group-[.active]:text-admin-blue transition-colors">
                                <i class="fas fa-building text-sm"></i>
                            </div>
                            <div class="flex flex-col">
                                <span
                                    class="font-bold text-slate-700 text-sm group-[.active]:text-admin-blue transition-colors">{{ $menu->name }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </aside>

        <main class="flex-1 admin-card" id="concernArea">
            <div class="flex flex-col items-center justify-center h-full text-slate-300 gap-4">
                <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center text-3xl">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="text-center">
                    <h2 class="text-slate-400!">No Concern Selected</h2>
                    <p class="text-xs">Choose a concern from the left to manage its contents.</p>
                </div>
            </div>
        </main>
    </div>
@endsection