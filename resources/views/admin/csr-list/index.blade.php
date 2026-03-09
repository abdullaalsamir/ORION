@extends('admin.layouts.app')
@section('title', 'CSR Management')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="flex flex-col">
                <h1>CSR Management</h1>
                <p class="text-xs text-slate-400">Manage CSR items and activities (16:9 Ratio)</p>
            </div>
            <button onclick="openCsrAddModal()" class="btn-success h-10!">
                <i class="fas fa-plus"></i> Add CSR Item
            </button>
        </div>

        <div class="admin-card-body bg-slate-50/20 custom-scrollbar">
            <div class="space-y-3">
                @forelse($groupedCsr as $date => $items)
                    <div class="csr-sortable-list space-y-3" data-date="{{ $date }}">
                        @foreach($items as $item)
                            <div class="sortable-item group {{ $loop->parent->index % 2 == 0 ? 'bg-red-50/50 border-red-100' : 'bg-green-50/50 border-green-100' }} border rounded-2xl p-3 flex items-center hover:border-admin-blue transition-all"
                                data-id="{{ $item->id }}">

                                <div
                                    class="drag-handle w-8 flex justify-center {{ count($items) > 1 ? 'cursor-grab active:cursor-grabbing text-slate-300 hover:text-admin-blue' : 'opacity-0 pointer-events-none' }}">
                                    <i class="fas fa-arrows-up-down-left-right"></i>
                                </div>

                                <div
                                    class="w-40 aspect-video rounded-xl overflow-hidden bg-slate-100 border border-slate-200 shrink-0 ml-2">
                                    <img src="{{ url($menu->full_slug . '/' . basename($item->image_path)) }}"
                                        class="w-full h-full object-cover transition-all duration-500">
                                </div>

                                <div class="flex-1 min-w-0 flex flex-col gap-0.5 ml-4 self-start">
                                    <span class="font-bold text-slate-700 text-sm capitalize truncate tracking-tight mt-1">
                                        {{ $item->title }}
                                    </span>

                                    <div class="text-[11px] text-slate-400 line-clamp-1 mt-1">
                                        {!! $item->description !!}
                                    </div>

                                    <div
                                        class="flex items-center gap-1.5 text-[10px] font-bold text-admin-blue uppercase tracking-wider mt-2">
                                        <i class="far fa-calendar-alt text-[9px]"></i>
                                        {{ date('d F, Y', strtotime($date)) }}
                                    </div>
                                </div>

                                <div class="shrink-0 px-4">
                                    <span class="badge {{ $item->is_active ? 'badge-success' : 'badge-danger' }}">
                                        {{ $item->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>

                                <div class="flex items-center border-l pl-4 border-slate-100 space-x-1">
                                    <button class="btn-icon w-8 p-1.5!"
                                        onclick="openCsrEditModal({{ json_encode($item) }}, '{{ $menu->full_slug }}')">
                                        <i class="fas fa-pencil text-xs"></i>
                                    </button>
                                    <button class="btn-danger w-8 p-1.5!" onclick="deleteCsr({{ $item->id }})">
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @empty
                    <div
                        class="flex flex-col items-center justify-center py-20 bg-white border-2 border-dashed border-slate-200 rounded-3xl text-slate-300">
                        <i class="fas fa-folder-open text-4xl mb-4"></i>
                        <h2 class="text-slate-400!">No CSR Items Found</h2>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div id="addModal" class="modal-overlay hidden">
        <div class="modal-content max-w-2xl! h-[90vh]! flex flex-col">
            <div class="flex justify-between items-center mb-6 pb-3 border-b border-slate-100 shrink-0">
                <h1 class="mb-0!">Add CSR Item</h1>
                <button type="button" onclick="closeModal('addModal')" class="btn-icon"><i
                        class="fas fa-times text-xl"></i></button>
            </div>

            <form action="{{ route('admin.csr.store') }}" method="POST" enctype="multipart/form-data"
                class="flex-1 overflow-y-auto custom-scrollbar pr-2 space-y-6">
                @csrf
                <div class="flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-slate-400 uppercase ml-1 mb-1 block">Event Image (16:9)</label>
                    <input type="file" name="image" id="addInput" accept="image/*" class="hidden"
                        onchange="handlePreview(this, 'addPreview')">
                    <div class="aspect-video bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center overflow-hidden cursor-pointer hover:border-admin-blue transition-all group"
                        id="addPreview" onclick="document.getElementById('addInput').click()">
                        <i
                            class="fas fa-camera text-3xl text-slate-300 mb-2 group-hover:text-admin-blue transition-colors"></i>
                        <span class="text-slate-400 font-bold text-[10px] uppercase tracking-widest text-center px-4">Select
                            Image</span>
                    </div>
                    <span id="addImgError" class="text-[10px] text-red-500 font-bold uppercase mt-1 hidden ml-1">Please
                        select an image</span>
                </div>

                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-8 relative flex flex-col gap-1">
                        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">CSR Title</label>
                        <input type="text" name="title" id="addTitle" required class="input-field w-full">
                    </div>
                    <div class="col-span-4 flex flex-col gap-1">
                        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Event Date</label>
                        <input type="date" name="csr_date" required class="input-field w-full">
                    </div>
                </div>

                <div class="relative flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Description</label>
                    <textarea name="description" id="addDesc" required
                        class="input-field w-full h-32 py-3 resize-none custom-scrollbar"></textarea>
                </div>

                <div class="flex justify-end pt-4 sticky bottom-0 bg-white border-t border-slate-50">
                    <button type="submit" class="btn-success h-10">Save CSR Item</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal-overlay hidden">
        <div class="modal-content max-w-2xl! h-[90vh]! flex flex-col">
            <div class="flex justify-between items-center mb-6 pb-3 border-b border-slate-100 shrink-0">
                <h1 class="mb-0!">Edit CSR Item</h1>
                <button onclick="closeModal('editModal')" class="btn-icon"><i class="fas fa-times text-xl"></i></button>
            </div>

            <form id="editForm" class="flex-1 overflow-y-auto custom-scrollbar pr-2 space-y-6">
                @csrf
                <input type="file" name="image" id="editInput" accept="image/*" class="hidden"
                    onchange="handlePreview(this, 'editPreview')">
                <div class="flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-slate-400 uppercase mb-1 block ml-1">Change Image</label>
                    <div class="relative group cursor-pointer aspect-video"
                        onclick="document.getElementById('editInput').click()">
                        <div class="w-full h-full bg-slate-100 rounded-3xl border border-slate-200 overflow-hidden"
                            id="editPreview"></div>
                        <div
                            class="absolute inset-0 bg-admin-blue/60 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all rounded-3xl">
                            <span class="text-white font-bold text-[10px] uppercase tracking-widest">Click to Replace
                                Image</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-8 relative flex flex-col gap-1">
                        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">CSR Title</label>
                        <input type="text" name="title" id="editTitle" required class="input-field w-full">
                    </div>
                    <div class="col-span-4 flex flex-col gap-1">
                        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Event Date</label>
                        <input type="date" name="csr_date" id="editDate" required class="input-field w-full">
                    </div>
                </div>

                <div class="relative flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Description</label>
                    <textarea name="description" id="editDesc" required
                        class="input-field w-full h-32 py-3 resize-none custom-scrollbar"></textarea>
                </div>

                <div
                    class="flex items-center justify-between mt-4 sticky bottom-0 bg-white pb-2 pt-4 border-t border-slate-50">
                    <label class="toggle-switch">
                        <input type="checkbox" id="editActive" name="is_active">
                        <div class="toggle-bg"></div>
                        <span id="csrStatusLabel" class="ml-3 font-bold text-slate-600 text-sm">Active</span>
                    </label>
                    <button type="submit" class="btn-primary h-10">Update CSR Item</button>
                </div>
            </form>
        </div>
    </div>
@endsection