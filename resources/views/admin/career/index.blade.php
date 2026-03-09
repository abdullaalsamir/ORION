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
                @foreach($careers as $job)
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

                        <div class="flex items-center border-l pl-4 border-slate-100 space-x-1">
                            <button class="btn-icon w-8 p-1.5!" onclick="openCareerEditModal({{ json_encode($job) }})">
                                <i class="fas fa-pencil text-xs"></i>
                            </button>
                            <button class="btn-danger w-8 p-1.5!" onclick="deleteCareer({{ $job->id }})">
                                <i class="fas fa-trash-can text-xs"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @foreach(['add', 'edit'] as $mode)
        <div id="{{ $mode }}Modal" class="modal-overlay hidden">
            <div class="modal-content max-w-3xl! h-[90vh]! flex flex-col relative">

                <div id="{{ $mode }}Overlay"
                    class="absolute inset-0 bg-white/80 z-50 flex-col items-center justify-center hidden rounded-2xl">
                    <i class="fas fa-spinner fa-spin text-4xl text-admin-blue mb-3"></i>
                    <p class="text-sm font-bold text-slate-600">Processing PDF Pages...</p>
                </div>

                <div class="flex justify-between items-center mb-6 pb-3 border-b border-slate-100 shrink-0">
                    <h1 class="mb-0!">{{ $mode === 'add' ? 'Post New Job' : 'Edit Job Post' }}</h1>
                    <button type="button" onclick="closeModal('{{ $mode }}Modal')" class="btn-icon"><i
                            class="fas fa-times text-xl"></i></button>
                </div>

                <form id="{{ $mode }}Form" action="{{ $mode === 'add' ? route('admin.career.store') : '' }}" method="POST"
                    enctype="multipart/form-data" class="flex-1 overflow-y-auto custom-scrollbar pr-2 space-y-5">
                    @csrf
                    @if($mode === 'edit') @method('PUT') @endif

                    <div class="relative w-full">
                        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Job Title</label>
                        <input type="text" name="title" id="{{ $mode }}Title" required class="input-field w-full">
                    </div>

                    <div class="relative w-full">
                        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Location</label>
                        <input type="text" name="location" id="{{ $mode }}Location" required class="input-field w-full">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">On / From</label>
                            <input type="date" name="on_from" id="{{ $mode }}From" class="input-field w-full">
                        </div>
                        <div class="relative">
                            <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">On / To</label>
                            <input type="date" name="on_to" id="{{ $mode }}To" class="input-field w-full">
                        </div>
                    </div>

                    <div class="relative w-full">
                        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Job Type</label>
                        <select name="job_type" id="{{ $mode }}JobType" class="input-field w-full">
                            <option value="Full-Time">Full-Time</option>
                            <option value="Part-Time">Part-Time</option>
                            <option value="Internship">Internship</option>
                        </select>
                    </div>

                    <div class="relative w-full">
                        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Apply Type</label>
                        <select name="apply_type" id="{{ $mode }}ApplyType" class="input-field w-full">
                            <option value="Online">Online</option>
                            <option value="Offline">Offline</option>
                        </select>
                    </div>

                    <div class="relative w-full">
                        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Attach Banner / PDF Document</label>
                        <input type="file" name="file" id="{{ $mode }}File" accept=".jpg,.jpeg,.png,.webp,.pdf"
                            class="input-field w-full p-2" onchange="processFileSelection(this, '{{ $mode }}')">
                        <div id="{{ $mode }}PdfInputs"></div>
                    </div>

                    <div class="relative w-full">
                        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Job Description &
                            Requirements</label>
                        <textarea name="description" id="{{ $mode }}Desc"
                            class="input-field w-full h-40 py-3 resize-none"></textarea>
                    </div>

                    @if($mode === 'edit')
                        <div class="flex items-center mt-2 pt-2 border-t border-slate-50">
                            <label class="toggle-switch">
                                <input type="checkbox" id="editActive" name="is_active">
                                <div class="toggle-bg"></div>
                                <span id="careerStatusLabel" class="ml-3 font-bold text-slate-600">Active</span>
                            </label>
                        </div>
                    @endif

                    <div class="flex justify-end pt-4 sticky bottom-0 bg-white border-t border-slate-50 z-10">
                        <button type="submit" id="{{ $mode }}SubmitBtn"
                            class="btn-success h-10">{{ $mode === 'add' ? 'Post Job' : 'Update Job' }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endsection