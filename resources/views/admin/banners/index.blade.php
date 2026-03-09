@extends('admin.layouts.app')
@section('title', 'Banner Management')

@section('content')
    <div class="flex gap-6 h-full overflow-hidden">
        <aside class="w-80 bg-white rounded-2xl border border-slate-200 flex flex-col overflow-hidden">
            <div class="admin-card-header">
                <div class="flex flex-col">
                    <h1>Pages</h1>
                    <p class="text-xs text-slate-400">Select a page to manage banners</p>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-2 custom-scrollbar bg-slate-50/30">
                @foreach($leafMenus as $menu)
                    <div class="leaf-menu-item p-3.5 bg-white border border-slate-200 rounded-2xl hover:border-admin-blue/50 cursor-pointer transition-all group"
                        data-id="{{ $menu->id }}" data-slug="{{ $menu->full_slug }}"
                        onclick="loadBanners({{ $menu->id }}, this)">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 group-hover:text-admin-blue transition-colors">
                                <i class="fas fa-image text-sm"></i>
                            </div>
                            <div class="flex flex-col">
                                <span
                                    class="font-bold text-slate-700 text-sm group-[.active]:text-admin-blue transition-colors">{{ $menu->name }}</span>
                                <span
                                    class="text-[9px] uppercase text-slate-400 tracking-tighter">{!! strip_tags($menu->parent_path) !!}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </aside>

        <main class="flex-1 admin-card" id="imageArea">
            <div class="flex flex-col items-center justify-center h-full text-slate-300 gap-4">
                <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center text-3xl">
                    <i class="fas fa-images"></i>
                </div>
                <div class="text-center">
                    <h2 class="text-slate-400!">No Page Selected</h2>
                    <p class="text-xs">Choose a page from the left to manage its banners.</p>
                </div>
            </div>
        </main>
    </div>

    <div id="uploadModal" class="modal-overlay hidden">
        <div class="modal-content max-w-xl w-full">
            <div class="flex justify-between items-center mb-8 pb-3 border-b border-slate-200">
                <h1 class="mb-0!">Upload Banner</h1>
                <button onclick="closeModal('uploadModal')" class="btn-icon"><i class="fas fa-times text-xl"></i></button>
            </div>

            <form id="uploadForm" class="flex-1 overflow-y-auto flex flex-col gap-6 pr-2 custom-scrollbar">
                @csrf
                <input type="file" name="image" id="uploadInput" accept="image/*" class="hidden"
                    onchange="handlePreview(this, 'uploadPreviewContainer')">

                <div class="aspect-video w-full bg-slate-50 rounded-2xl flex items-center justify-center">
                    <div id="uploadPreviewContainer" style="aspect-ratio: 48/9;"
                        class="w-full border-2 border-dashed border-slate-300 rounded-2xl flex flex-col items-center justify-center overflow-hidden cursor-pointer hover:border-admin-blue hover:bg-slate-100/50 transition-all group relative"
                        onclick="document.getElementById('uploadInput').click()">
                        <i
                            class="fas fa-cloud-arrow-up text-2xl text-slate-300 mb-2 group-hover:text-admin-blue transition-colors"></i>
                        <span id="uploadPlaceholderText"
                            class="text-slate-400 text-[10px] uppercase tracking-widest text-center px-2">Click to select
                            48:9 image</span>
                    </div>
                </div>

                <div class="flex gap-6">
                    <div class="flex-1 flex flex-col gap-2">
                        <div class="flex justify-between text-[11px] font-bold text-slate-400">
                            <span class="text-[11px] cursor-pointer hover:text-admin-blue transition-colors"
                                onclick="document.getElementById('ratioSlider').value=0; document.getElementById('ratioSlider').dispatchEvent(new Event('input'))">48:9</span>
                            <span class="text-[11px] cursor-pointer hover:text-admin-blue transition-colors"
                                onclick="document.getElementById('ratioSlider').value=1; document.getElementById('ratioSlider').dispatchEvent(new Event('input'))">23:9</span>
                            <span class="text-[11px] cursor-pointer hover:text-admin-blue transition-colors"
                                onclick="document.getElementById('ratioSlider').value=2; document.getElementById('ratioSlider').dispatchEvent(new Event('input'))">16:9</span>
                        </div>
                        <input type="range" id="ratioSlider" name="ratio" min="0" max="2" value="0" step="1"
                            class="w-full cursor-pointer accent-admin-blue mt-3">
                    </div>

                    <div class="w-20 flex flex-col gap-2">
                        <label class="text-[11px] font-bold text-slate-400 text-center uppercase tracking-wider">Max
                            Width</label>
                        <input type="text" id="maxWidthInput" name="max_width" value="2000"
                            class="input-field text-center text-slate-700"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <span id="maxWidthError" class="text-[10px] text-red-500 text-center hidden font-bold -mt-1">Range:
                            500-2000</span>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="btn-success h-10">Upload Banner</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal-overlay hidden">
        <div class="modal-content max-w-xl w-full">
            <div class="flex justify-between items-center mb-6 pb-3 border-b border-slate-200">
                <h1 class="mb-0!">Edit Banner Status</h1>
                <button onclick="closeModal('editModal')" class="btn-icon"><i class="fas fa-times text-xl"></i></button>
            </div>

            <form id="editForm" class="flex-1 overflow-y-auto flex flex-col gap-6 pr-2 custom-scrollbar">
                @csrf @method('PUT')

                <div class="aspect-video bg-slate-50 rounded-2xl overflow-hidden flex items-center justify-center"
                    id="editPreviewContainer">
                </div>

                <div class="flex items-center justify-between mt-2">
                    <label class="toggle-switch">
                        <input type="checkbox" id="editActiveToggle" name="is_active">
                        <div class="toggle-bg"></div>
                        <span id="editStatusLabel" class="ml-3 font-bold text-slate-600">Active</span>
                    </label>

                    <button type="submit" id="editSubmit" class="btn-primary h-10">Update Banner</button>
                </div>
            </form>
        </div>
    </div>
@endsection