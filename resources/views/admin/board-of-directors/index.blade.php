@extends('admin.layouts.app')
@section('title', 'Board of Directors Management')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="flex flex-col">
                <h1>Board of Directors</h1>
                <p class="text-xs text-slate-400">Manage profiles and reorder using drag-and-drop</p>
            </div>
            <button onclick="openDirectorAddModal()" class="btn-success h-10!">
                <i class="fas fa-plus"></i> Add Director
            </button>
        </div>

        <div class="admin-card-body bg-slate-50/20 custom-scrollbar">
            <div id="directors-sortable-list" class="space-y-3">
                @forelse($items as $item)
                    <div class="sortable-item group bg-white border border-slate-200 rounded-2xl p-3 flex items-center hover:border-admin-blue transition-all"
                        data-id="{{ $item->id }}">
                        <div
                            class="drag-handle w-8 flex justify-center cursor-grab active:cursor-grabbing text-slate-300 hover:text-admin-blue transition-colors">
                            <i class="fas fa-arrows-up-down-left-right"></i>
                        </div>

                        <div
                            class="w-20 aspect-3/4 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 shrink-0 ml-2">
                            <img src="{{ asset('storage/' . $item->image_path) }}"
                                class="w-full h-full object-cover transition-all duration-500">
                        </div>

                        <div class="flex-1 min-w-0 flex flex-col gap-0.5 ml-4 self-start">
                            <span
                                class="capitalize font-semibold text-slate-700 text-sm truncate tracking-tight mt-1">{{ $item->name }}</span>
                            <span
                                class="text-admin-blue font-semibold text-[11px] truncate tracking-wider">{{ $item->designation }}</span>
                            <p class="text-[11px] text-slate-400 line-clamp-3 mt-1">{{ strip_tags($item->description) }}
                            </p>
                        </div>

                        <div class="shrink-0 px-4">
                            <span class="badge {{ $item->is_active ? 'badge-success' : 'badge-danger' }}">
                                {{ $item->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="flex items-center border-l pl-4 border-slate-100 space-x-1">
                            <button class="btn-icon w-8 p-1.5!"
                                onclick="openDirectorEditModal({{ json_encode($item) }}, '{{ $menu->full_slug }}')">
                                <i class="fas fa-pencil text-xs"></i>
                            </button>
                            <button class="btn-danger w-8 p-1.5!" onclick="deleteDirector({{ $item->id }})">
                                <i class="fas fa-trash-can text-xs"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div
                        class="flex flex-col items-center justify-center py-20 bg-white border-2 border-dashed border-slate-200 rounded-xl text-slate-300">
                        <i class="fas fa-user-tie text-4xl mb-4"></i>
                        <h2 class="text-slate-400!">No Directors Found</h2>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div id="addModal" class="modal-overlay hidden">
        <div class="modal-content max-w-5xl! h-[90vh]! flex flex-col">

            <div class="flex justify-between items-center pb-3 border-b border-slate-100 shrink-0">
                <h1 class="mb-0!">Add New Director</h1>
                <button type="button" onclick="closeModal('addModal')" class="btn-icon">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="addDirectorForm" action="{{ route('admin.directors.store') }}" method="POST"
                enctype="multipart/form-data" class="flex-1 overflow-y-auto custom-scrollbar px-2 py-6">
                @csrf
                <div class="grid grid-cols-15 gap-10">
                    <div class="col-span-4 flex flex-col items-center">
                        <label class="text-[11px] font-bold text-slate-400 uppercase mb-1 self-start ml-1">Director Photo
                            (3:4)</label>
                        <input type="file" name="image" id="addInput" accept="image/*" class="hidden"
                            onchange="handlePreview(this, 'addPreview')">
                        <div class="w-full aspect-3/4 bg-slate-50 rounded-xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center overflow-hidden cursor-pointer hover:border-admin-blue transition-all group"
                            id="addPreview" onclick="document.getElementById('addInput').click()">
                            <i class="fas fa-camera text-3xl text-slate-300 mb-2 group-hover:text-admin-blue"></i>
                            <span
                                class="text-slate-400 font-bold text-[9px] uppercase tracking-widest text-center px-4 opacity-60">Upload
                                Portrait</span>
                        </div>
                        <span id="addImgError" class="text-[10px] text-red-500 font-bold uppercase mt-2 hidden ml-1">Photo
                            is required</span>
                    </div>

                    <div class="col-span-11 space-y-5">
                        <div class="flex flex-col gap-1">
                            <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Full Name</label>
                            <input type="text" name="name" required class="input-field w-full">
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Designation</label>
                            <input type="text" name="designation" required class="input-field w-full"
                                placeholder="e.g. Managing Director">
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Biography /
                                Description</label>
                            <textarea name="description" id="addDesc" required
                                class="input-field w-full h-72 py-3 resize-none custom-scrollbar"></textarea>
                        </div>
                    </div>
                </div>
            </form>

            <div class="flex justify-end items-center border-t border-slate-100 pt-4 shrink-0 bg-white">
                <button type="submit" form="addDirectorForm" class="btn-success h-10">
                    Save Director
                </button>
            </div>
        </div>
    </div>

    <div id="editModal" class="modal-overlay hidden">
        <div class="modal-content max-w-5xl! h-[90vh]! flex flex-col">

            <div class="flex justify-between items-center pb-3 border-b border-slate-100 shrink-0">
                <h1 class="mb-0!">Edit Director Profile</h1>
                <button onclick="closeModal('editModal')" class="btn-icon">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="editForm" class="flex-1 overflow-y-auto custom-scrollbar px-2 py-6 space-y-6">
                @csrf
                <input type="file" name="image" id="editInput" accept="image/*" class="hidden"
                    onchange="handlePreview(this, 'editPreview')">

                <div class="grid grid-cols-15 gap-10">
                    <div class="col-span-4">
                        <label class="text-[11px] font-bold text-slate-400 uppercase mb-1 block ml-1">Director Photo</label>
                        <div class="relative group cursor-pointer w-full aspect-3/4"
                            onclick="document.getElementById('editInput').click()">
                            <div class="w-full h-full bg-slate-100 rounded-xl overflow-hidden" id="editPreview"></div>
                            <div
                                class="absolute inset-0 bg-admin-blue/60 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all rounded-xl">
                                <span class="text-white font-bold text-[9px] uppercase tracking-widest">Replace Photo</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-11 space-y-5">
                        <div class="flex flex-col gap-1">
                            <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Full Name</label>
                            <input type="text" name="name" id="editName" required class="input-field w-full">
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Designation</label>
                            <input type="text" name="designation" id="editDesignation" required class="input-field w-full">
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Biography /
                                Description</label>
                            <textarea name="description" id="editDesc" required
                                class="input-field w-full h-72 py-3 resize-none custom-scrollbar"></textarea>
                        </div>
                    </div>
                </div>
            </form>

            <div class="flex items-center justify-between border-t border-slate-100 pt-4 shrink-0 bg-white">
                <label class="toggle-switch">
                    <input type="checkbox" id="editActive" name="is_active">
                    <div class="toggle-bg"></div>
                    <span id="directorStatusLabel" class="ml-3 font-bold text-slate-600 text-sm">
                        Active
                    </span>
                </label>

                <button type="submit" form="editForm" class="btn-primary h-10">
                    Update Profile
                </button>
            </div>
        </div>
    </div>
@endsection