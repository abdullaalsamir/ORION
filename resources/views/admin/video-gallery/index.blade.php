@extends('admin.layouts.app')
@section('title', 'Video Gallery Management')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="flex flex-col">
                <h1>Video Gallery</h1>
                <p class="text-xs text-slate-400">Manage videos and reorder using drag-and-drop</p>
            </div>
            <button onclick="openVideoAddModal()" class="btn-success h-10!">
                <i class="fas fa-plus"></i> Add Video
            </button>
        </div>

        <div class="admin-card-body bg-slate-50/20 custom-scrollbar">
            <div id="video-sortable-list" class="space-y-3">
                @forelse($items as $item)
                    <div class="sortable-item group bg-white border border-slate-200 rounded-2xl p-3 flex items-center hover:border-admin-blue transition-all"
                        data-id="{{ $item->id }}">

                        <div
                            class="drag-handle w-8 flex justify-center cursor-grab active:cursor-grabbing text-slate-300 hover:text-admin-blue transition-colors">
                            <i class="fas fa-arrows-up-down-left-right"></i>
                        </div>

                        <div
                            class="w-40 aspect-video rounded-xl overflow-hidden bg-slate-100 border border-slate-200 shrink-0 ml-2 relative flex items-center justify-center">

                            <div class="absolute inset-0 shimmer" id="shimmer-{{ $item->id }}"></div>

                            <img src="{{ asset('storage/video-gallery/thumbnails/thumbs/' . basename($item->thumbnail_path)) }}?v={{ $item->updated_at->timestamp }}"
                                class="w-full h-full object-cover transition-opacity duration-300 opacity-0"
                                onload="this.classList.remove('opacity-0'); document.getElementById('shimmer-{{ $item->id }}').remove();">

                            <div
                                class="absolute inset-0 flex items-center justify-center pointer-events-none transition-opacity">
                                <span class="fa-stack text-base">
                                    <i class="fas fa-circle text-cyan-500 fa-stack-2x"></i>
                                    <i class="fas fa-play text-white fa-stack-1x scale-75"></i>
                                </span>
                            </div>
                        </div>

                        <div class="flex-1 min-w-0 flex flex-col gap-0.5 ml-4 self-start">
                            <span class="font-bold text-slate-700 text-sm capitalize truncate tracking-tight mt-1">
                                {{ $item->title }}
                            </span>

                            <div class="text-[11px] text-slate-400 line-clamp-1 mt-1 flex items-center gap-1.5">
                                <i class="fas fa-file-video text-admin-blue/70"></i>
                                {{ basename($item->video_path) }}
                            </div>
                        </div>

                        <div class="shrink-0 px-4 flex items-center gap-3">
                            <a href="{{ asset('storage/' . $item->video_path) }}" target="_blank" title="Play Video"
                                class="badge badge-info hover:bg-sky-100 transition-colors">
                                <i class="fas fa-eye opacity-70"></i>
                            </a>

                            <span class="badge {{ $item->is_active ? 'badge-success' : 'badge-danger' }}">
                                {{ $item->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="flex items-center border-l pl-4 border-slate-100 space-x-1">
                            <button class="btn-icon w-8 p-1.5!" onclick="openVideoEditModal({{ json_encode($item) }})">
                                <i class="fas fa-pencil text-xs"></i>
                            </button>
                            <button class="btn-danger w-8 p-1.5!" onclick="deleteVideo({{ $item->id }})">
                                <i class="fas fa-trash-can text-xs"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div
                        class="flex flex-col items-center justify-center py-20 bg-white border-2 border-dashed border-slate-200 rounded-3xl text-slate-300">
                        <i class="fas fa-film text-4xl mb-4"></i>
                        <h2 class="text-slate-400!">No Videos Found</h2>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div id="addModal" class="modal-overlay hidden">
        <div class="modal-content max-w-3xl! flex flex-col">

            <div class="flex justify-between items-center pb-3 border-b border-slate-100 shrink-0">
                <h1 class="mb-0!">Add New Video</h1>
                <button type="button" onclick="closeModal('addModal')" class="btn-icon">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="addVideoForm" class="flex-1 overflow-y-auto custom-scrollbar py-6 space-y-6">
                @csrf
                <div class="flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Video Title</label>
                    <input type="text" name="title" required class="input-field w-full">
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="flex flex-col items-center">
                        <label class="text-[11px] font-bold text-slate-400 uppercase mb-1 self-start ml-1">Thumbnail
                            (16:9)</label>
                        <input type="file" name="thumbnail" id="addThumbInput" accept="image/*" class="hidden" required
                            onchange="handlePreview(this, 'addThumbPreview')">
                        <div class="w-full aspect-video bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center overflow-hidden cursor-pointer hover:border-admin-blue transition-all group"
                            id="addThumbPreview" onclick="document.getElementById('addThumbInput').click()">
                            <i
                                class="fas fa-image text-3xl text-slate-300 mb-2 group-hover:text-admin-blue transition-colors"></i>
                            <span
                                class="text-slate-400 font-bold text-[10px] uppercase tracking-widest text-center px-4">Upload
                                Cover</span>
                        </div>
                    </div>

                    <div class="flex flex-col items-center">
                        <label class="text-[11px] font-bold text-slate-400 uppercase mb-1 self-start ml-1">Video File (Any
                            Format)</label>
                        <input type="file" name="video" id="addVideoInput" accept="video/*" class="hidden" required
                            onchange="document.getElementById('addVideoName').innerText = this.files[0].name">
                        <div class="w-full aspect-video bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center cursor-pointer hover:border-admin-blue transition-all group px-4 text-center"
                            onclick="document.getElementById('addVideoInput').click()">
                            <i
                                class="fas fa-film text-3xl text-slate-300 mb-2 group-hover:text-admin-blue transition-colors"></i>
                            <span id="addVideoName"
                                class="text-slate-400 font-bold text-[10px] uppercase tracking-widest break-all mt-1">
                                Select Video File<br>(Max 200MB)
                            </span>
                        </div>
                    </div>
                </div>
            </form>

            <div class="flex justify-end items-center border-t border-slate-100 pt-4 shrink-0 bg-white">
                <button type="submit" form="addVideoForm" id="addSubmitBtn" class="btn-success h-10">
                    Upload Video
                </button>
            </div>
        </div>
    </div>

    <div id="editModal" class="modal-overlay hidden">
        <div class="modal-content max-w-3xl! flex flex-col">

            <div class="flex justify-between items-center pb-3 border-b border-slate-100 shrink-0">
                <h1 class="mb-0!">Edit Video</h1>
                <button type="button" onclick="closeModal('editModal')" class="btn-icon">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="editForm" class="flex-1 overflow-y-auto custom-scrollbar py-6 space-y-6">
                @csrf
                <div class="flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Video Title</label>
                    <input type="text" name="title" id="editTitle" required class="input-field w-full">
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="flex flex-col items-center">
                        <label class="text-[11px] font-bold text-slate-400 uppercase mb-1 self-start ml-1">Replace
                            Thumbnail</label>
                        <input type="file" name="thumbnail" id="editThumbInput" accept="image/*" class="hidden"
                            onchange="handlePreview(this, 'editThumbPreview')">
                        <div class="w-full aspect-video bg-slate-50 rounded-2xl border border-slate-200 flex flex-col items-center justify-center overflow-hidden cursor-pointer hover:border-admin-blue transition-all group relative"
                            onclick="document.getElementById('editThumbInput').click()">
                            <div id="editThumbPreview" class="w-full h-full bg-slate-100"></div>
                            <div
                                class="absolute inset-0 bg-admin-blue/60 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all">
                                <span class="text-white font-bold text-[10px] uppercase tracking-widest">Replace
                                    Cover</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col items-center">
                        <label class="text-[11px] font-bold text-slate-400 uppercase mb-1 self-start ml-1">Replace Video
                            File</label>
                        <input type="file" name="video" id="editVideoInput" accept="video/*" class="hidden"
                            onchange="document.getElementById('editVideoName').innerText = this.files[0].name">
                        <div class="w-full aspect-video bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center cursor-pointer hover:border-admin-blue transition-all group px-4 text-center"
                            onclick="document.getElementById('editVideoInput').click()">
                            <i
                                class="fas fa-upload text-3xl text-slate-300 mb-2 group-hover:text-admin-blue transition-colors"></i>
                            <span id="editVideoName"
                                class="text-slate-400 font-bold text-[10px] uppercase tracking-widest break-all mt-1">
                                Click to Replace Video<br>(Max 200MB)
                            </span>
                        </div>
                    </div>
                </div>
            </form>

            <div class="flex items-center justify-between border-t border-slate-100 pt-4 shrink-0 bg-white">
                <label class="toggle-switch">
                    <input type="checkbox" id="editActive" name="is_active">
                    <div class="toggle-bg"></div>
                    <span id="videoStatusLabel" class="ml-3 font-bold text-slate-600 text-sm">Active</span>
                </label>

                <button type="submit" form="editForm" id="editSubmitBtn" class="btn-primary h-10">
                    Update Video
                </button>
            </div>
        </div>
    </div>
@endsection