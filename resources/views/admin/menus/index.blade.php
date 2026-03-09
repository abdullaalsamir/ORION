@extends('admin.layouts.app')
@section('title', 'Menu Management')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header items-start!">
            <div class="flex flex-col">
                <h1>Menu Management</h1>
                <p class="text-xs text-slate-400">Organize site menus and their children</p>
            </div>

            <form method="POST" action="{{ route('admin.menus.store') }}"
                class="flex items-center gap-3 bg-slate-50 p-1.5 rounded-2xl border border-slate-100">
                @csrf
                <input type="text" name="name" placeholder="Menu Name..." required class="input-field h-10! w-48">

                <select name="parent_id" class="input-field h-10! w-44">
                    <option value="">⁝⁝⁝ Main Menu ⁝⁝⁝</option>
                    @foreach($menus as $menu)
                        <option value="{{ $menu->id }}">{{ $menu->name }}</option>
                        @foreach($menu->children as $child)
                            <option value="{{ $child->id }}" class="text-slate-400">— {{ $child->name }}</option>
                        @endforeach
                    @endforeach
                </select>

                <div class="flex bg-white p-1 rounded-xl border border-slate-200 h-10 items-center">
                    <label class="cursor-pointer flex items-center h-full">
                        <input type="radio" name="is_multifunctional" value="0" checked class="peer sr-only">
                        <span
                            class="px-3 py-1.5 rounded-lg text-[10px] font-bold text-slate-400 border border-transparent peer-checked:text-sky-600 peer-checked:border-slate-200 transition-all uppercase">Functional</span>
                    </label>
                    <label class="cursor-pointer flex items-center h-full">
                        <input type="radio" name="is_multifunctional" value="1" class="peer sr-only">
                        <span
                            class="px-3 py-1.5 rounded-lg text-[10px] font-bold text-slate-400 border border-transparent peer-checked:bg-purple-600 peer-checked:text-white peer-checked:border-transparent transition-all uppercase">Multifunctional</span>
                    </label>
                </div>

                <button type="submit" class="btn-success h-10!">
                    <i class="fas fa-plus"></i> Add Menu
                </button>
            </form>
        </div>

        <div class="admin-card-body custom-scrollbar bg-slate-50/20">
            <ul id="root-menu-list" class="menu-sortable-list space-y-3">
                <li
                    class="p-4.5 mb-6 bg-white border border-slate-200 rounded-2xl flex items-center justify-between opacity-60">
                    <div class="flex items-center gap-4">
                        <div class="w-5"></div>
                        <div class="w-5"></div>
                        <div class="flex flex-row items-center gap-2">
                            <span class="font-bold text-slate-700">{{ $homeMenu->name }}</span>
                            <span class="badge badge-info text-[9px]">System Default</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="badge badge-info text-[9px]">Functional</span>
                        <span class="badge badge-success text-[9px]">Active</span>
                        <div class="flex gap-1 border-l pl-4 border-transparent">
                            <div class="w-8"></div>
                            <div class="w-8"></div>
                        </div>
                    </div>
                </li>

                @foreach($menus as $menu)
                    @include('admin.menus.partials.menu-item', ['menu' => $menu])
                @endforeach
            </ul>
        </div>
    </div>

    <div id="editModal" class="modal-overlay hidden">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-8 pb-2 border-b border-slate-100">
                <h1 class="mb-0!">Edit Menu</h1>
                <button onclick="closeModal('editModal')" class="btn-icon"><i class="fas fa-times text-xl"></i></button>
            </div>

            <form method="POST" id="editForm" class="flex flex-col gap-5">
                @csrf @method('PUT')

                <div class="flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Menu Name</label>
                    <input type="text" name="name" id="editName" required class="input-field w-full">
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Parent Category</label>
                    <select name="parent_id" id="editParent" class="input-field w-full">
                        <option value="" data-active="1">⁝⁝⁝ Main Menu ⁝⁝⁝</option>
                        @foreach($menus as $m)
                            <option value="{{ $m->id }}" data-active="{{ $m->isEffectivelyActive() ? '1' : '0' }}">
                                {{ $m->name }}
                            </option>
                            @foreach($m->children as $c)
                                <option value="{{ $c->id }}" data-active="{{ $c->isEffectivelyActive() ? '1' : '0' }}"
                                    class="text-slate-400">
                                    — {{ $c->name }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Menu Type</label>
                    <div class="flex bg-white p-1 rounded-xl border border-slate-200 h-12 w-full items-center">
                        <label class="cursor-pointer flex-1 flex items-center justify-center h-full">
                            <input type="radio" name="is_multifunctional" value="0" id="edit-type-functional"
                                class="peer sr-only">
                            <span
                                class="w-full text-center px-5 py-2 rounded-lg text-sm text-slate-400 border border-transparent peer-checked:text-sky-600 peer-checked:border-slate-200 transition-all uppercase">Functional</span>
                        </label>
                        <label class="cursor-pointer flex-1 flex items-center justify-center h-full">
                            <input type="radio" name="is_multifunctional" value="1" id="edit-type-multi"
                                class="peer sr-only">
                            <span
                                class="w-full text-center px-5 py-2 rounded-lg text-sm text-slate-400 peer-checked:bg-purple-600 peer-checked:text-white transition-all uppercase">Multifunctional</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-4">
                    <label class="toggle-switch">
                        <input type="checkbox" id="editActive" name="is_active">
                        <div class="toggle-bg"></div>
                        <span id="toggleLabel" class="ml-3 font-bold text-slate-600">Active</span>
                    </label>

                    <button type="submit" class="btn-primary h-10">Update Menu</button>
                </div>
            </form>
        </div>
    </div>
@endsection