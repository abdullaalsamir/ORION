@extends('admin.layouts.app')
@section('title', 'Career Management')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="flex flex-col">
                <h1>Career Management</h1>
                <p class="text-xs text-slate-400">Manage job openings and career opportunities</p>
            </div>
            <button onclick="openCareerAddModal()" class="btn-success h-10!">
                <i class="fas fa-plus"></i> Add Job
            </button>
        </div>

        <div class="admin-card-body bg-slate-50/20 custom-scrollbar">
            <div id="career-list" class="space-y-4">
                @forelse($careers as $job)
                    <div class="sortable-item group bg-white border border-slate-200 rounded-2xl p-4 flex items-center hover:border-admin-blue transition-all"
                        data-id="{{ $job->id }}">
                        <div
                            class="drag-handle w-8 flex justify-center cursor-grab active:cursor-grabbing p-1.5 text-slate-300 hover:text-admin-blue transition-colors">
                            <i class="fas fa-arrows-up-down-left-right"></i>
                        </div>

                        <div class="flex-1 min-w-0 flex flex-col gap-1 ml-4">
                            <span
                                class="font-bold text-slate-700 text-sm truncate uppercase tracking-tight">{{ $job->title }}</span>
                            <div class="flex gap-4 text-[11px] text-slate-500 font-medium">
                                <span><i class="fas fa-location-dot text-slate-300 mr-1"></i>
                                    {{ $job->location }}</span>
                                <span class="text-orion-blue"><i class="fas fa-briefcase mr-1"></i> {{ $job->job_type }}</span>
                                <span class="text-slate-400"><i class="fas fa-globe mr-1"></i> {{ $job->apply_type }}
                                    Apply</span>
                            </div>
                        </div>

                        <div class="shrink-0 px-4 flex items-center gap-3">
                            @if($job->file_path)
                                <a href="{{ asset('storage/' . $job->file_path) }}" target="_blank" title="View Attachment"
                                    class="badge badge-info hover:bg-sky-100 transition-colors">
                                    <i class="fas fa-eye opacity-70"></i>
                                </a>
                            @endif

                            <span class="badge {{ $job->is_active ? 'badge-success' : 'badge-danger' }}">
                                {{ $job->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="flex items-center border-l pl-4 border-slate-100 space-x-1">
                            <button class="btn-icon w-8 p-1.5!" onclick="openCareerEditModal({{ json_encode($job) }})">
                                <i class="fas fa-pencil text-xs"></i>
                            </button>
                            <button class="btn-danger w-8 p-1.5!" onclick="deleteCareer({{ $job->id }})">
                                <i class="fas fa-trash-can text-xs"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div
                        class="flex flex-col items-center justify-center py-20 bg-white border-2 border-dashed border-slate-200 rounded-3xl text-slate-300">
                        <i class="fas fa-briefcase text-4xl mb-4"></i>
                        <h2 class="text-slate-400!">No Jobs Found</h2>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div id="addModal" class="modal-overlay hidden">
        <div class="modal-content max-w-3xl! h-[90vh]! flex flex-col relative">

            <div id="addOverlay"
                class="absolute inset-0 bg-white/80 z-50 flex-col items-center justify-center hidden rounded-2xl">
                <i class="fas fa-spinner fa-spin text-4xl text-admin-blue mb-3"></i>
                <p class="text-sm font-bold text-slate-600">Processing PDF Pages...</p>
            </div>

            <div class="flex justify-between items-center pb-3 border-b border-slate-100 shrink-0">
                <h1 class="mb-0!">Post New Job</h1>
                <button type="button" onclick="closeModal('addModal')" class="btn-icon">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="addForm" action="{{ route('admin.career.store') }}" method="POST" enctype="multipart/form-data"
                class="flex-1 overflow-y-auto custom-scrollbar pr-2 py-6 space-y-6">
                @csrf

                <div class="flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Job Title</label>
                    <input type="text" name="title" id="addTitle" required class="input-field w-full">
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Location</label>
                    <input type="text" name="location" id="addLocation" required class="input-field w-full">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">On / From</label>
                        <input type="date" name="on_from" id="addFrom" class="input-field w-full">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">On / To</label>
                        <input type="date" name="on_to" id="addTo" class="input-field w-full">
                    </div>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Job Type</label>
                    <select name="job_type" id="addJobType" class="input-field w-full">
                        <option value="Full-Time">Full-Time</option>
                        <option value="Part-Time">Part-Time</option>
                        <option value="Internship">Internship</option>
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Apply Type</label>
                    <select name="apply_type" id="addApplyType" class="input-field w-full">
                        <option value="Online">Online</option>
                        <option value="Offline">Offline</option>
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Attach Banner / PDF Document</label>
                    <input type="file" name="file" id="addFile" accept=".jpg,.jpeg,.png,.webp,.pdf"
                        class="input-field w-full p-2" onchange="processFileSelection(this, 'add')">
                    <div id="addPdfInputs"></div>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Job Description &
                        Requirements</label>
                    <textarea name="description" id="addDesc"
                        class="input-field w-full h-40 py-3 resize-none custom-scrollbar"></textarea>
                </div>
            </form>

            <div class="flex justify-end items-center border-t border-slate-100 pt-4 shrink-0 bg-white">
                <button type="submit" form="addForm" id="addSubmitBtn" class="btn-success h-10">Post Job</button>
            </div>
        </div>
    </div>

    <div id="editModal" class="modal-overlay hidden">
        <div class="modal-content max-w-3xl! h-[90vh]! flex flex-col relative">

            <div id="editOverlay"
                class="absolute inset-0 bg-white/80 z-50 flex-col items-center justify-center hidden rounded-2xl">
                <i class="fas fa-spinner fa-spin text-4xl text-admin-blue mb-3"></i>
                <p class="text-sm font-bold text-slate-600">Processing PDF Pages...</p>
            </div>

            <div class="flex justify-between items-center pb-3 border-b border-slate-100 shrink-0">
                <h1 class="mb-0!">Edit Job Post</h1>
                <button type="button" onclick="closeModal('editModal')" class="btn-icon">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="editForm" action="" method="POST" enctype="multipart/form-data"
                class="flex-1 overflow-y-auto custom-scrollbar pr-2 py-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Job Title</label>
                    <input type="text" name="title" id="editTitle" required class="input-field w-full">
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Location</label>
                    <input type="text" name="location" id="editLocation" required class="input-field w-full">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">On / From</label>
                        <input type="date" name="on_from" id="editFrom" class="input-field w-full">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">On / To</label>
                        <input type="date" name="on_to" id="editTo" class="input-field w-full">
                    </div>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Job Type</label>
                    <select name="job_type" id="editJobType" class="input-field w-full">
                        <option value="Full-Time">Full-Time</option>
                        <option value="Part-Time">Part-Time</option>
                        <option value="Internship">Internship</option>
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Apply Type</label>
                    <select name="apply_type" id="editApplyType" class="input-field w-full">
                        <option value="Online">Online</option>
                        <option value="Offline">Offline</option>
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Attach Banner / PDF Document</label>
                    <input type="file" name="file" id="editFile" accept=".jpg,.jpeg,.png,.webp,.pdf"
                        class="input-field w-full p-2" onchange="processFileSelection(this, 'edit')">
                    <div id="editPdfInputs"></div>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Job Description &
                        Requirements</label>
                    <textarea name="description" id="editDesc"
                        class="input-field w-full h-40 py-3 resize-none custom-scrollbar"></textarea>
                </div>
            </form>

            <div class="flex items-center justify-between border-t border-slate-100 pt-4 shrink-0 bg-white">
                <label class="toggle-switch">
                    <input type="checkbox" id="editActive" name="is_active" form="editForm">
                    <div class="toggle-bg"></div>
                    <span id="careerStatusLabel" class="ml-3 font-bold text-slate-600 text-sm">Active</span>
                </label>

                <button type="submit" form="editForm" id="editSubmitBtn" class="btn-primary h-10">Update Job</button>
            </div>
        </div>
    </div>
@endsection