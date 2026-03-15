<div class="admin-card-header shrink-0">
    <div class="flex flex-col">
        <h1>{{ $menu->name }}</h1>
        <p class="text-xs text-slate-400">Manage information, photos, and details</p>
    </div>
</div>

<div class="admin-card-body custom-scrollbar bg-slate-50/20 px-6 py-4 space-y-8">

    <div class="flex items-end gap-4">
        <form id="infoForm" onsubmit="submitInfo(event, {{ $menu->id }})" class="flex-1 flex items-end gap-4">
            <div class="flex-1 flex flex-col gap-1">
                <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Web Address</label>
                <input type="url" name="web_address" class="input-field w-full" placeholder="e.g., https://example.com"
                    value="{{ $concern->web_address }}">
            </div>

            <button type="submit" class="btn-primary h-10 shrink-0">Update</button>
        </form>

        <div class="flex items-center gap-2 border-l border-slate-200 pl-4 h-10 shrink-0">
            <label class="toggle-switch">
                <input type="checkbox" name="is_redirect" id="redirectToggle" {{ $concern->is_redirect ? 'checked' : '' }} onchange="toggleRedirectLabel(this, {{ $menu->id }})">
                <div class="toggle-bg"></div>
                <span class="ml-3 font-bold text-sm {{ $concern->is_redirect ? 'text-admin-blue' : 'text-slate-600' }}"
                    id="redirectLabel">
                    Redirect {{ $concern->is_redirect ? 'On' : 'Off' }}
                </span>
            </label>
        </div>
    </div>

    <hr class="border-slate-100">

    <div>
        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1 block mb-2">Cover Photo (10:4)</label>
        <input type="file" id="coverUploadInput" class="hidden" accept="image/*"
            onchange="submitCover(this, {{ $menu->id }})">

        @if($concern->cover_photo_path)
            <div
                class="relative group w-full aspect-10/4 rounded-2xl overflow-hidden border border-slate-200 shimmer bg-slate-100">
                <img src="{{ url($menu->full_slug . '/' . basename($concern->cover_photo_path)) }}?v={{ time() }}"
                    class="w-full h-full object-cover" onload="this.parentElement.classList.remove('shimmer')">
                <div class="absolute inset-0 bg-admin-blue/60 opacity-0 group-hover:opacity-100 flex items-center justify-center cursor-pointer transition-all backdrop-blur-sm"
                    onclick="document.getElementById('coverUploadInput').click()">
                    <span class="text-white font-bold tracking-widest uppercase text-sm"><i
                            class="fas fa-camera mr-2"></i>Replace Photo</span>
                </div>
                <button type="button" onclick="deleteCover({{ $menu->id }})"
                    class="absolute top-4 right-4 bg-red-500 text-white w-10 h-10 rounded-xl opacity-0 group-hover:opacity-100 transition-all z-10 flex items-center justify-center shadow-lg hover:bg-red-600 cursor-pointer">
                    <i class="fas fa-trash-can"></i>
                </button>
            </div>
        @else
            <div class="w-full aspect-10/4 bg-white border-2 border-dashed border-slate-300 rounded-2xl flex flex-col items-center justify-center cursor-pointer hover:border-admin-blue group hover:bg-slate-50 transition-all"
                onclick="document.getElementById('coverUploadInput').click()">
                <i
                    class="fas fa-cloud-arrow-up text-3xl text-slate-300 mb-2 group-hover:text-admin-blue transition-colors"></i>
                <span
                    class="text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center px-4 opacity-60">Click
                    to select 10:4 image</span>
            </div>
        @endif
    </div>

    <hr class="border-slate-100">

    <div>
        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1 block mb-2">Photo Gallery (23:9)</label>
        <input type="file" id="galleryUploadInput" class="hidden" accept="image/*" multiple
            onchange="submitGallery(this, {{ $menu->id }})">
        <input type="file" id="galleryReplaceInput" class="hidden" accept="image/*"
            onchange="submitReplaceGallery(this)">

        <div class="w-full aspect-23/9 bg-white border-2 border-dashed border-slate-300 rounded-2xl flex flex-col items-center justify-center cursor-pointer hover:border-admin-blue group hover:bg-slate-50 transition-all mb-4"
            onclick="document.getElementById('galleryUploadInput').click()">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center px-4"><i
                    class="fas fa-images mr-2"></i>Click to select multiple photos</span>
        </div>

        @if($galleries->count() > 0)
            <div id="gallery-sortable" class="grid grid-cols-2 gap-4">
                @foreach($galleries as $g)
                    <div class="sortable-gallery-item relative group w-full aspect-23/9 rounded-xl overflow-hidden border border-slate-200 bg-slate-100 shimmer"
                        data-id="{{ $g->id }}">
                        <div
                            class="drag-handle absolute top-3 left-3 z-10 w-8 h-8 bg-white/90 backdrop-blur rounded text-slate-400 cursor-grab flex items-center justify-center hover:text-admin-blue shadow-sm">
                            <i class="fas fa-arrows-up-down-left-right"></i>
                        </div>
                        <img src="{{ url($menu->full_slug . '/' . basename($g->file_path)) }}?v={{ time() }}"
                            class="w-full h-full object-cover" onload="this.parentElement.classList.remove('shimmer')">
                        <div class="absolute inset-0 bg-admin-blue/60 opacity-0 group-hover:opacity-100 flex items-center justify-center cursor-pointer transition-all backdrop-blur-sm"
                            onclick="triggerGalleryReplace({{ $g->id }})">
                            <span class="text-white font-bold tracking-widest uppercase text-[10px]"><i
                                    class="fas fa-camera mr-2"></i>Replace Photo</span>
                        </div>
                        <button type="button" onclick="deleteGalleryItem({{ $g->id }})"
                            class="absolute top-3 right-3 bg-red-500 text-white w-8 h-8 rounded-lg opacity-0 group-hover:opacity-100 transition-all z-10 flex items-center justify-center shadow hover:bg-red-600 cursor-pointer">
                            <i class="fas fa-trash-can text-xs"></i>
                        </button>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <hr class="border-slate-100">

    <form id="descForm" onsubmit="submitDesc(event, {{ $menu->id }})" class="pb-6">
        <div class="flex items-center justify-between mb-2">
            <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Description</label>
            <button type="submit" class="btn-primary h-9 px-6">Update Description</button>
        </div>
        <textarea id="concernDesc" name="description" class="hidden">{{ $concern->description }}</textarea>
    </form>

</div>