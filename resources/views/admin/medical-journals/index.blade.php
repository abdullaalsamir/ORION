@extends('admin.layouts.app')
@section('title', 'Medical Journals Management')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="flex flex-col">
                <h1>Medical Journals</h1>
                <p class="text-xs text-slate-400">Organize PDFs by year and drag to reorder within groups
                </p>
            </div>
            <button onclick="openJournalAddModal()" class="btn-success h-10!">
                <i class="fas fa-plus"></i> Add Journal
            </button>
        </div>

        <div class="admin-card-body bg-slate-50/20 custom-scrollbar">
            <div class="space-y-4">
                @forelse($groupedJournals as $year => $medicalJournals)
                    <div class="journal-sortable-list p-4 rounded-3xl {{ $loop->index % 2 == 0 ? 'bg-red-50/50 border-red-100' : 'bg-green-50/50 border-green-100' }} border space-y-3"
                        data-year="{{ $year }}">

                        <div class="flex items-center gap-2 mb-2 ml-1">
                            <span
                                class="text-xl font-bold uppercase tracking-[0.1em] {{ $loop->index % 2 == 0 ? 'text-red-500' : 'text-green-500' }}">
                                {{ $year }}
                            </span>
                        </div>

                        @foreach($medicalJournals as $mj)
                            <div class="sortable-item group bg-white border border-slate-200 rounded-2xl p-3 flex items-center hover:border-admin-blue transition-all"
                                data-id="{{ $mj->id }}">

                                <div
                                    class="drag-handle w-8 flex justify-center {{ count($medicalJournals) > 1 ? 'cursor-grab active:cursor-grabbing text-slate-300 hover:text-admin-blue' : 'opacity-0 pointer-events-none' }}">
                                    <i class="fas fa-arrows-up-down-left-right"></i>
                                </div>

                                <div class="w-12 h-12 flex items-center justify-center text-red-500 shrink-0">
                                    <i class="fas fa-file-pdf text-3xl"></i>
                                </div>

                                <div class="flex-1 min-w-0 flex flex-col gap-0.5 ml-2">
                                    <span class="font-semibold text-slate-700 text-sm truncate tracking-tight">
                                        {{ $mj->title }}
                                    </span>
                                </div>

                                <div class="shrink-0 px-4 flex items-center gap-3">
                                    <a href="{{ url($menu->full_slug . '/' . $mj->year . '/' . $mj->filename) }}" target="_blank"
                                        class="badge badge-info hover:bg-sky-100 transition-colors">
                                        <i class="fas fa-eye opacity-70"></i>
                                    </a>
                                    <span class="badge {{ $mj->is_active ? 'badge-success' : 'badge-danger' }}">
                                        {{ $mj->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>

                                <div class="flex items-center border-l pl-4 border-slate-100 space-x-1">
                                    <button class="btn-icon w-8 p-1.5!"
                                        onclick="openJournalEditModal({{ json_encode($mj) }}, '{{ $menu->full_slug }}')">
                                        <i class="fas fa-pencil text-xs"></i>
                                    </button>
                                    <button class="btn-danger w-8 p-1.5!" onclick="deleteJournal({{ $mj->id }})">
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @empty
                    <div
                        class="flex flex-col items-center justify-center py-20 bg-white border-2 border-dashed border-slate-200 rounded-3xl text-slate-300">
                        <i class="fas fa-book-medical text-4xl mb-4"></i>
                        <h2 class="text-slate-400!">No Medical Journals Found</h2>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div id="addModal" class="modal-overlay hidden">
        <div class="modal-content max-w-xl! flex flex-col">
            <div class="flex justify-between items-center mb-6 pb-3 border-b border-slate-100 shrink-0">
                <h1 class="mb-0!">Add New Journal</h1>
                <button type="button" onclick="closeModal('addModal')" class="btn-icon"><i
                        class="fas fa-times text-xl"></i></button>
            </div>

            <form action="{{ route('admin.medical-journals.store') }}" method="POST" enctype="multipart/form-data"
                class="flex-1 overflow-y-auto custom-scrollbar pr-2 space-y-6">
                @csrf
                <div class="flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-slate-400 uppercase ml-1 block mb-1">Select PDF File</label>
                    <input type="file" name="pdf" id="pdfInput" accept="application/pdf" required class="hidden"
                        onchange="handlePdfSelect(this)">
                    <div class="aspect-10/2 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center overflow-hidden cursor-pointer hover:border-admin-blue transition-all group"
                        id="pdfPlaceholder" onclick="document.getElementById('pdfInput').click()">
                        <i
                            class="fas fa-file-pdf text-3xl text-slate-300 mb-2 group-hover:text-red-500 transition-colors"></i>
                        <span id="pdfStatusText"
                            class="text-slate-400 font-bold text-[10px] uppercase tracking-widest text-center px-4">Click to
                            select PDF</span>
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-8 flex flex-col gap-1">
                        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Journal Title</label>
                        <input type="text" name="title" id="titleInput" required class="input-field w-full"
                            placeholder="Auto-filled from filename">
                    </div>
                    <div class="col-span-4 flex flex-col gap-1">
                        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Publication Year</label>
                        <select name="year" required class="input-field w-full">
                            @for($y = date('Y'); $y >= 1985; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="flex justify-end pt-4 sticky bottom-0 bg-white">
                    <button type="submit" class="btn-success h-10">Upload Journal</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal-overlay hidden">
        <div class="modal-content max-w-xl! flex flex-col">
            <div class="flex justify-between items-center mb-6 pb-3 border-b border-slate-100 shrink-0">
                <h1 class="mb-0!">Edit Journal Details</h1>
                <button onclick="closeModal('editModal')" class="btn-icon"><i class="fas fa-times text-xl"></i></button>
            </div>

            <form id="editForm" class="flex-1 overflow-y-auto custom-scrollbar pr-2 space-y-6">
                @csrf
                <input type="file" name="pdf" id="editPdfInput" accept="application/pdf" class="hidden"
                    onchange="handlePdfSelect(this, true)">

                <div class="flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-slate-400 uppercase mb-1 block">Change PDF File
                        (Optional)</label>
                    <div class="aspect-10/2 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center overflow-hidden cursor-pointer hover:border-admin-blue transition-all group"
                        id="editPdfPlaceholder" onclick="document.getElementById('editPdfInput').click()">
                        <i class="fas fa-file-pdf text-3xl text-slate-300 mb-2 group-hover:text-red-500 transition-colors">
                        </i>
                        <span id="editPdfStatus"
                            class="text-slate-400 font-bold text-[10px] uppercase tracking-widest text-center px-4">
                            Click to replace PDF
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-8 flex flex-col gap-1">
                        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Journal Title</label>
                        <input type="text" name="title" id="editTitle" required class="input-field w-full">
                    </div>
                    <div class="col-span-4 flex flex-col gap-1">
                        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Year</label>
                        <select name="year" id="editYear" required class="input-field w-full">
                            @for($y = date('Y'); $y >= 1985; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div
                    class="flex items-center justify-between mt-4 sticky bottom-0 bg-white pb-2 pt-4 border-t border-slate-50">
                    <label class="toggle-switch">
                        <input type="checkbox" id="editActive" name="is_active">
                        <div class="toggle-bg"></div>
                        <span id="journalStatusLabel" class="ml-3 font-bold text-slate-600 text-sm">Active</span>
                    </label>
                    <button type="submit" class="btn-primary h-10">Update Journal</button>
                </div>
            </form>
        </div>
    </div>
@endsection