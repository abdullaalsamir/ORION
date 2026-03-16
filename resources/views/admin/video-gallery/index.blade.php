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
            <div id="video-sortable-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-4">
                @forelse($items as $item)
                    <div class="sortable-video-item relative group bg-white border border-slate-200 rounded-2xl p-2 hover:border-admin-blue transition-all flex flex-col"
                        data-id="{{ $item->id }}">

                        <div
                            class="drag-handle absolute top-4 left-4 z-10 w-8 h-8 bg-white/90 backdrop-blur rounded text-slate-400 cursor-grab flex items-center justify-center hover:text-admin-blue shadow-sm">
                            <i class="fas fa-arrows-up-down-left-right"></i>
                        </div>

                        <div class="w-full aspect-video rounded-xl overflow-hidden bg-slate-100 relative mb-3">
                            <img src="{{ asset('storage/' . $item->thumbnail_path) }}?v={{ time() }}"
                                class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/20 flex items-center justify-center pointer-events-none">
                                <i class="fas fa-play-circle text-white text-4xl opacity-80"></i>
                            </div>
                        </div>

                        <div class="px-2 flex flex-col flex-1 pb-2">
                            <h3 class="font-bold text-slate-700 text-sm line-clamp-2 mb-2">{{ $item->title }}</h3>

                            <div class="mt-auto flex items-center justify-between">
                                <span class="badge {{ $item->is_active ? 'badge-success' : 'badge-danger' }} text-[10px]!">
                                    {{ $item->is_active ? 'Active' : 'Inactive' }}
                                </span>

                                <div class="flex gap-2 border-l pl-3 border-slate-100">
                                    <button class="btn-icon w-8 p-1.5!" onclick="openVideoEditModal({{ json_encode($item) }})">
                                        <i class="fas fa-pencil text-xs"></i>
                                    </button>
                                    <button class="btn-danger w-8 p-1.5!" onclick="deleteVideo({{ $item->id }})">
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-full flex flex-col items-center justify-center py-20 bg-white border-2 border-dashed border-slate-200 rounded-xl text-slate-300">
                        <i class="fas fa-film text-4xl mb-4"></i>
                        <h2 class="text-slate-400!">No Videos Found</h2>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div id="addModal" class="modal-overlay hidden">
        <div class="modal-content max-w-2xl w-full">
            <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                <h1 class="mb-0!">Add New Video</h1>
                <button type="button" onclick="closeModal('addModal')" class="btn-icon"><i
                        class="fas fa-times text-xl"></i></button>
            </div>
            <form id="addVideoForm" class="py-6 space-y-6">
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
                        <div class="w-full aspect-video bg-slate-50 rounded-xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center overflow-hidden cursor-pointer hover:border-admin-blue transition-all group"
                            id="addThumbPreview" onclick="document.getElementById('addThumbInput').click()">
                            <i class="fas fa-image text-3xl text-slate-300 mb-2 group-hover:text-admin-blue"></i>
                            <span
                                class="text-slate-400 font-bold text-[9px] uppercase tracking-widest text-center px-4">Upload
                                Cover</span>
                        </div>
                    </div>

                    <div class="flex flex-col items-center">
                        <label class="text-[11px] font-bold text-slate-400 uppercase mb-1 self-start ml-1">Video File (Any
                            Format)</label>
                        <input type="file" name="video" id="addVideoInput" accept="video/*" class="hidden" required
                            onchange="document.getElementById('addVideoName').innerText = this.files[0].name">
                        <div class="w-full aspect-video bg-slate-50 rounded-xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center cursor-pointer hover:border-admin-blue transition-all group px-4 text-center"
                            onclick="document.getElementById('addVideoInput').click()">
                            <i class="fas fa-film text-3xl text-slate-300 mb-2 group-hover:text-admin-blue"></i>
                            <span id="addVideoName"
                                class="text-slate-400 font-bold text-[9px] uppercase tracking-widest break-all">Select Video
                                File<br>(Max 200MB)</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end border-t border-slate-100 pt-4">
                    <button type="submit" id="addSubmitBtn" class="btn-success h-10">Upload Video</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal-overlay hidden">
        <div class="modal-content max-w-2xl w-full">
            <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                <h1 class="mb-0!">Edit Video</h1>
                <button type="button" onclick="closeModal('editModal')" class="btn-icon"><i
                        class="fas fa-times text-xl"></i></button>
            </div>
            <form id="editForm" class="py-6 space-y-6">
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
                        <div class="w-full aspect-video bg-slate-50 rounded-xl border border-slate-200 flex flex-col items-center justify-center overflow-hidden cursor-pointer hover:border-admin-blue transition-all group relative"
                            onclick="document.getElementById('editThumbInput').click()">
                            <div id="editThumbPreview" class="w-full h-full"></div>
                            <div
                                class="absolute inset-0 bg-admin-blue/60 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all">
                                <span class="text-white font-bold text-[9px] uppercase tracking-widest">Replace Cover</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col items-center">
                        <label class="text-[11px] font-bold text-slate-400 uppercase mb-1 self-start ml-1">Replace Video
                            File</label>
                        <input type="file" name="video" id="editVideoInput" accept="video/*" class="hidden"
                            onchange="document.getElementById('editVideoName').innerText = this.files[0].name">
                        <div class="w-full aspect-video bg-slate-50 rounded-xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center cursor-pointer hover:border-admin-blue transition-all group px-4 text-center"
                            onclick="document.getElementById('editVideoInput').click()">
                            <i class="fas fa-upload text-3xl text-slate-300 mb-2 group-hover:text-admin-blue"></i>
                            <span id="editVideoName"
                                class="text-slate-400 font-bold text-[9px] uppercase tracking-widest break-all">Click to
                                Replace Video<br>(Max 200MB)</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between border-t border-slate-100 pt-4">
                    <label class="toggle-switch">
                        <input type="checkbox" id="editActive" name="is_active">
                        <div class="toggle-bg"></div>
                        <span id="videoStatusLabel" class="ml-3 font-bold text-slate-600 text-sm">Active</span>
                    </label>
                    <button type="submit" id="editSubmitBtn" class="btn-primary h-10">Update Video</button>
                </div>
            </form>
        </div>
    </div>
@endsection