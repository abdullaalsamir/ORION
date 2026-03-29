@extends('admin.layouts.app')
@section('title', 'Swiper Slider')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="flex flex-col">
                <h1>Swiper Slider Management</h1>
                <p class="text-xs text-slate-400">Manage home page slider images and content
                </p>
            </div>
            <button onclick="openSliderAddModal()" class="btn-success h-10!">
                <i class="fas fa-plus"></i> Add Slider
            </button>
        </div>

        <div class="admin-card-body bg-slate-50/20 custom-scrollbar">
            <div id="slider-list" class="space-y-4">
                @foreach($sliders as $slider)
                    <div class="sortable-item group bg-white border border-slate-200 rounded-2xl p-3 flex items-center hover:border-admin-blue transition-all"
                        data-id="{{ $slider->id }}">
                        <div
                            class="drag-handle w-8 flex justify-center cursor-grab active:cursor-grabbing p-1.5 text-slate-300 hover:text-admin-blue transition-colors">
                            <i class="fas fa-arrows-up-down-left-right"></i>
                        </div>

                        <div
                            class="w-56 aspect-23/9 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 shrink-0 ml-2 relative">

                            <div class="absolute inset-0 shimmer" id="shimmer-{{ $slider->id }}"></div>

                            <img src="{{ asset('storage/sliders/thumbs/' . basename($slider->image_path)) }}?v={{ $slider->updated_at->timestamp }}"
                                class="w-full h-full object-cover transition-opacity duration-300 opacity-0"
                                onload="this.classList.remove('opacity-0'); document.getElementById('shimmer-{{ $slider->id }}').remove();">
                        </div>

                        <div class="flex-1 min-w-0 flex flex-col gap-1 ml-4 self-start">
                            <span
                                class="font-semibold text-slate-900 text-sm truncate uppercase tracking-tight mt-1">{{ $slider->header_1 }}</span>
                            <span
                                class="font-semibold text-admin-blue text-sm truncate uppercase tracking-tight">{{ $slider->header_2 }}</span>
                            <p class="text-xs text-slate-400 line-clamp-1 mt-1">{{ $slider->description }}</p>
                        </div>

                        <div class="flex items-center gap-4 shrink-0 px-4">
                            @php
                                $cleanUrl = ltrim($slider->link_url, '/');
                                $linkedMenu = $allMenus->firstWhere('full_slug', $cleanUrl);
                            @endphp
                            @if($linkedMenu)
                                <span class="badge badge-info uppercase! tracking-normal!">
                                    <i class="fas fa-link mr-1 opacity-50"></i> {{ $linkedMenu->name }}
                                </span>
                            @endif
                            <span class="badge {{ $slider->is_active ? 'badge-success' : 'badge-danger' }}">
                                {{ $slider->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="flex items-center border-l pl-4 border-slate-100 space-x-1">
                            <button class="btn-icon w-8 p-1.5!" onclick="openSliderEditModal({{ json_encode($slider) }})">
                                <i class="fas fa-pencil text-xs"></i>
                            </button>
                            <button class="btn-danger w-8 p-1.5!" onclick="deleteSlider({{ $slider->id }})">
                                <i class="fas fa-trash-can text-xs"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div id="addModal" class="modal-overlay hidden">
        <div class="modal-content max-w-2xl! h-[90vh]! flex flex-col">
            <div class="flex justify-between items-center pb-3 border-b border-slate-100 shrink-0">
                <h1 class="mb-0!">Add New Slider</h1>
                <button type="button" onclick="closeModal('addModal')" class="btn-icon">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="addForm" action="{{ route('admin.sliders.store') }}" method="POST" enctype="multipart/form-data"
                class="flex-1 overflow-y-auto custom-scrollbar py-6 pr-2 space-y-5">
                @csrf
                <input type="file" name="image" id="addInput" accept="image/*" class="hidden"
                    onchange="handlePreview(this, 'addPreview')">
                <div class="aspect-23/9 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center overflow-hidden cursor-pointer hover:border-admin-blue hover:bg-slate-100 transition-all group relative shrink-0"
                    id="addPreview" onclick="document.getElementById('addInput').click()">
                    <i
                        class="fas fa-cloud-arrow-up text-2xl text-slate-300 mb-2 group-hover:text-admin-blue transition-colors"></i>
                    <span class="text-slate-400 font-bold text-[10px] uppercase tracking-widest">Click to select 23:9
                        image</span>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="relative">
                        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Header Line 1 (White)</label>
                        <input type="text" name="header_1" maxlength="30" required class="input-field w-full"
                            oninput="updateCount(this, 'addC1', 30)">
                        <span id="addC1" class="absolute right-3 bottom-2.5 text-[9px] text-slate-300 font-bold">0/30</span>
                    </div>
                    <div class="relative">
                        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Header Line 2 (Amber)</label>
                        <input type="text" name="header_2" maxlength="30" required class="input-field w-full"
                            oninput="updateCount(this, 'addC2', 30)">
                        <span id="addC2" class="absolute right-3 bottom-2.5 text-[9px] text-slate-300 font-bold">0/30</span>
                    </div>
                </div>

                <div class="relative">
                    <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Description Text (Optional)</label>
                    <textarea name="description" maxlength="150" class="input-field w-full h-24 py-3 resize-none"
                        oninput="updateCount(this, 'addCD', 150)"></textarea>
                    <span id="addCD" class="absolute right-3 bottom-2 text-[9px] text-slate-300 font-bold">0/150</span>
                </div>

                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-4 relative">
                        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Button Text</label>
                        <input type="text" name="button_text" value="Explore More" maxlength="15" class="input-field w-full"
                            oninput="updateCount(this, 'addCBT', 15)">
                        <span id="addCBT"
                            class="absolute right-3 bottom-2.5 text-[9px] text-slate-300 font-bold">12/15</span>
                    </div>
                    <div class="col-span-8">
                        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Redirect Link (Optional)</label>
                        <select name="link_url" class="input-field w-full">
                            @include('admin.sliders.partials.menu-options', ['menus' => $menus])
                        </select>
                    </div>
                </div>
            </form>

            <div class="flex justify-end items-center border-t border-slate-100 pt-4 shrink-0 bg-white">
                <button type="submit" form="addForm" class="btn-success h-10">Upload Slider</button>
            </div>
        </div>
    </div>

    <div id="editModal" class="modal-overlay hidden">
        <div class="modal-content max-w-2xl! h-[90vh]! flex flex-col">
            <div class="flex justify-between items-center pb-3 border-b border-slate-100 shrink-0">
                <h1 class="mb-0!">Edit Slider</h1>
                <button onclick="closeModal('editModal')" class="btn-icon"><i class="fas fa-times text-xl"></i></button>
            </div>

            <form id="editForm" class="flex-1 overflow-y-auto custom-scrollbar py-6 pr-2 space-y-5">
                @csrf
                <input type="file" name="image" id="editInput" accept="image/*" class="hidden"
                    onchange="handlePreview(this, 'editPreview')">

                <div class="relative group cursor-pointer shrink-0" onclick="document.getElementById('editInput').click()">
                    <div class="aspect-23/9 bg-slate-100 rounded-2xl border border-slate-200 overflow-hidden"
                        id="editPreview"></div>
                    <div
                        class="absolute inset-0 bg-admin-blue/60 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all rounded-2xl">
                        <span class="text-white font-bold text-xs uppercase tracking-widest">Click to replace image</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="relative">
                        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Header Line 1 (White)</label>
                        <input type="text" name="header_1" id="editH1" maxlength="30" required class="input-field w-full"
                            oninput="updateCount(this, 'editC1', 30)">
                        <span id="editC1"
                            class="absolute right-3 bottom-2.5 text-[9px] text-slate-300 font-bold">0/30</span>
                    </div>
                    <div class="relative">
                        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Header Line 2 (Amber)</label>
                        <input type="text" name="header_2" id="editH2" maxlength="30" required class="input-field w-full"
                            oninput="updateCount(this, 'editC2', 30)">
                        <span id="editC2"
                            class="absolute right-3 bottom-2.5 text-[9px] text-slate-300 font-bold">0/30</span>
                    </div>
                </div>

                <div class="relative">
                    <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Description Text (Optional)</label>
                    <textarea name="description" id="editDesc" maxlength="150"
                        class="input-field w-full h-24 py-3 resize-none"
                        oninput="updateCount(this, 'editCD', 150)"></textarea>
                    <span id="editCD" class="absolute right-3 bottom-2 text-[9px] text-slate-300 font-bold">0/150</span>
                </div>

                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-4 relative">
                        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Button Text</label>
                        <input type="text" name="button_text" id="editBT" maxlength="15" class="input-field w-full"
                            oninput="updateCount(this, 'editCBT', 15)">
                        <span id="editCBT"
                            class="absolute right-3 bottom-2.5 text-[9px] text-slate-300 font-bold">0/15</span>
                    </div>
                    <div class="col-span-8">
                        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Redirect Link (Optional)</label>
                        <select name="link_url" id="editLink" class="input-field w-full">
                            @include('admin.sliders.partials.menu-options', ['menus' => $menus])
                        </select>
                    </div>
                </div>
            </form>

            <div class="flex items-center justify-between border-t border-slate-100 pt-4 shrink-0 bg-white">
                <label class="toggle-switch">
                    <input type="checkbox" id="editActive" name="is_active">
                    <div class="toggle-bg"></div>
                    <span id="sliderStatusLabel" class="ml-3 font-bold text-slate-600">Active</span>
                </label>

                <button type="submit" form="editForm" class="btn-primary h-10">Update Slider</button>
            </div>
        </div>
    </div>
@endsection