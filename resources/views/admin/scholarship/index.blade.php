@extends('admin.layouts.app')
@section('title', 'Scholarship Management')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header shrink-0">
            <div class="flex flex-col">
                <h1>Scholarship Management</h1>
                <p class="text-xs text-slate-400">Manage and organize scholarship recipients</p>
            </div>
            <button onclick="openScholarAddModal()" class="btn-success h-10!">
                <i class="fas fa-plus"></i> Add Person
            </button>
        </div>

        <div class="flex-1 flex flex-col min-h-0 bg-white overflow-hidden">
            <table class="w-full table-fixed border-collapse shrink-0">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="w-12 p-4 border-r border-slate-100"></th>
                        <th
                            class="w-72 p-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest border-r border-slate-100">
                            Name, Degree & Session</th>
                        <th
                            class="p-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest border-r border-slate-100">
                            Medical College</th>
                        <th
                            class="w-24 p-4 text-center text-[11px] font-bold text-slate-400 uppercase tracking-widest border-r border-slate-100">
                            Photo</th>
                        <th class="w-48 p-4 text-right text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                            Action</th>
                        <th class="w-2.5"></th>
                    </tr>
                </thead>
            </table>

            <div class="flex-1 overflow-y-scroll custom-scrollbar" style="scrollbar-gutter: stable;">
                <table class="w-full table-fixed border-collapse">
                    <tbody id="scholar-sortable-list">
                        @foreach($items as $item)
                            <tr data-id="{{ $item->id }}"
                                class="sortable-item group border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                                <td class="w-12 p-4 text-center border-r border-slate-100">
                                    <div
                                        class="drag-handle cursor-grab active:cursor-grabbing text-slate-300 hover:text-admin-blue transition-colors">
                                        <i class="fas fa-arrows-up-down-left-right"></i>
                                    </div>
                                </td>
                                <td class="w-72 p-4 border-r border-slate-100">
                                    <div class="flex flex-col">
                                        <span
                                            class="font-bold text-slate-700 text-sm leading-tight mb-1">{{ $item->name }}</span>
                                        <span class="text-[10px] text-slate-400 font-medium">{{ $item->degree }}</span>
                                        <div class="flex flex-col gap-0.5">
                                            @if($item->session)<span
                                            class="text-[10px] text-slate-400 font-medium">{{ $item->session }}</span>@endif
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 border-r border-slate-100 text-xs text-slate-500 font-medium leading-relaxed">
                                    {{ $item->medical_college }}
                                </td>
                                <td class="w-24 p-2 border-r border-slate-100 aspect-9/11">
                                    <div class="flex items-center justify-center w-full h-full">
                                        <img src="{{ url($menu->full_slug . '/' . basename($item->image_path)) }}"
                                            class="w-full h-full object-cover rounded-lg bg-slate-100" alt="">
                                    </div>
                                </td>
                                <td class="w-48 p-4">
                                    <div class="flex items-center justify-end gap-3">
                                        <span
                                            class="badge {{ $item->is_active ? 'badge-success' : 'badge-danger' }} text-[8px]!">
                                            {{ $item->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                        <div class="flex items-center border-l pl-3 border-slate-100 space-x-1">
                                            <button class="btn-icon w-8 p-1.5!"
                                                onclick="openScholarEditModal({{ json_encode($item) }}, '{{ $menu->full_slug }}')">
                                                <i class="fas fa-pencil text-xs"></i>
                                            </button>
                                            <button type="button" onclick="deleteScholarship({{ $item->id }})"
                                                class="btn-danger w-8 p-1.5!">
                                                <i class="fas fa-trash-can text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="addModal" class="modal-overlay hidden">
        <div class="modal-content max-w-2xl! flex flex-col">
            <div class="flex justify-between items-center mb-6 pb-3 border-b border-slate-100 shrink-0">
                <h1 class="mb-0!">Add New Person</h1>
                <button type="button" onclick="closeModal('addModal')" class="btn-icon"><i
                        class="fas fa-times text-xl"></i></button>
            </div>

            <form action="{{ route('admin.scholarship.store') }}" method="POST" enctype="multipart/form-data"
                class="flex-1 overflow-y-auto custom-scrollbar pr-2 space-y-6">
                @csrf
                <div class="grid grid-cols-12 gap-10">
                    <div class="col-span-4 flex flex-col items-center">
                        <label class="text-[11px] font-bold text-slate-400 uppercase mb-2 self-start ml-1">Photo
                            (9:11)</label>
                        <input type="file" name="image" id="addInput" accept="image/*" class="hidden"
                            onchange="handlePreview(this, 'addPreview')">
                        <div class="w-full aspect-9/11 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center overflow-hidden cursor-pointer hover:border-admin-blue hover:bg-slate-100 transition-all group"
                            id="addPreview" onclick="document.getElementById('addInput').click()">
                            <i
                                class="fas fa-camera text-3xl text-slate-300 mb-2 group-hover:text-admin-blue transition-colors"></i>
                            <span
                                class="text-slate-400 font-bold text-[9px] uppercase tracking-widest text-center px-4 opacity-60">Upload
                                Portrait</span>
                        </div>
                        <span id="addImgError" class="text-[10px] text-red-500 font-bold uppercase mt-2 hidden">Photo is
                            required</span>
                    </div>

                    <div class="col-span-8 space-y-5">
                        <div class="flex flex-col gap-1">
                            <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Full Name</label>
                            <input type="text" name="name" required class="input-field w-full"
                                placeholder="Enter recipient's name">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1">
                                <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Degree</label>
                                <input type="text" name="degree" required class="input-field w-full"
                                    placeholder="e.g. MBBS">
                            </div>

                            <div class="flex flex-col gap-1">
                                <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Session</label>
                                <input type="text" name="session" class="input-field w-full" placeholder="e.g. 2023-24">
                            </div>
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Medical College Name</label>
                            <input type="text" name="medical_college" required class="input-field w-full"
                                placeholder="Enter college name">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-6 sticky bottom-0 bg-white">
                    <button type="submit" class="btn-success h-10">Save Recipient</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal-overlay hidden">
        <div class="modal-content max-w-2xl! flex flex-col">
            <div class="flex justify-between items-center mb-6 pb-3 border-b border-slate-100 shrink-0">
                <h1 class="mb-0!">Edit Recipient</h1>
                <button onclick="closeModal('editModal')" class="btn-icon"><i class="fas fa-times text-xl"></i></button>
            </div>

            <form id="editForm" class="flex-1 overflow-y-auto custom-scrollbar pr-2 space-y-6">
                @csrf
                <input type="file" name="image" id="editInput" accept="image/*" class="hidden"
                    onchange="handlePreview(this, 'editPreview')">

                <div class="grid grid-cols-12 gap-10">
                    <div class="col-span-4 flex flex-col items-center">
                        <label class="text-[11px] font-bold text-slate-400 uppercase mb-2 self-start ml-1">Recipient
                            Photo</label>
                        <div class="relative group cursor-pointer w-full aspect-9/11"
                            onclick="document.getElementById('editInput').click()">
                            <div class="w-full h-full bg-slate-100 rounded-3xl border border-slate-200 overflow-hidden"
                                id="editPreview"></div>
                            <div
                                class="absolute inset-0 bg-admin-blue/60 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all rounded-3xl">
                                <span
                                    class="text-white font-bold text-[9px] uppercase tracking-widest text-center px-2">Replace
                                    Photo</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-8 space-y-5">
                        <div class="flex flex-col gap-1">
                            <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Full Name</label>
                            <input type="text" name="name" id="editName" required class="input-field w-full">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1">
                                <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Degree</label>
                                <input type="text" name="degree" id="editDegree" required class="input-field w-full">
                            </div>

                            <div class="flex flex-col gap-1">
                                <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Session</label>
                                <input type="text" name="session" id="editSession" class="input-field w-full">
                            </div>
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Medical College Name</label>
                            <input type="text" name="medical_college" id="editCollege" required class="input-field w-full">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-6 sticky bottom-0 bg-white pb-2 pt-4">
                    <label class="toggle-switch">
                        <input type="checkbox" id="editActive" name="is_active">
                        <div class="toggle-bg"></div>
                        <span id="scholarStatusLabel" class="ml-3 font-bold text-slate-600 text-sm">Active</span>
                    </label>
                    <button type="submit" class="btn-primary h-10">Update Details</button>
                </div>
            </form>
        </div>
    </div>
@endsection