@extends('admin.layouts.app')
@section('title', 'Page Management')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header items-start!">
            <div class="flex flex-col">
                <h1>Page Management</h1>
                <p class="text-xs text-slate-400">Edit content for menu, submenu and sub-submenu pages</p>
            </div>
        </div>

        <div class="admin-card-body custom-scrollbar bg-slate-50/20">
            <ul id="root-menu-list" class="space-y-3">
                <li
                    class="p-3 mb-6 bg-white border border-slate-200 rounded-2xl flex items-center justify-between opacity-60">
                    <div class="flex items-center gap-4">
                        <div class="w-6"></div>
                        <div class="flex flex-row items-center gap-2">
                            <span class="font-bold text-slate-700">{{ $homeMenu->name }}</span>
                            <span class="badge badge-info text-[9px]">System Default</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        @if(empty($homeMenu->content))
                            <span class="badge badge-danger text-[9px]!">No Content</span>
                        @else
                            <span class="badge badge-success text-[9px]!">Content Added</span>
                        @endif

                        <div class="flex items-center border-l pl-4 border-slate-100 space-x-1">
                            <button class="btn-icon edit-page w-8 p-1.5!" data-id="{{ $homeMenu->id }}"
                                data-name="Home Page" data-content="{{ e($homeMenu->content) }}">
                                <i class="fas fa-pencil text-xs"></i>
                            </button>
                        </div>
                    </div>
                </li>

                @foreach($menus as $menu)
                    @include('admin.pages.partials.page-item', ['menu' => $menu])
                @endforeach
            </ul>
        </div>
    </div>

    <div id="pageModal" class="modal-overlay hidden">
        <div class="modal-content max-w-6xl! h-[90vh]! flex flex-col">
            <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                <h1 id="modalTitle" class="mb-0!">Edit Page</h1>
                <button onclick="closeModal('pageModal')" class="btn-icon"><i class="fas fa-times text-xl"></i></button>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar py-6">
                <div class="grid grid-cols-12 gap-8 h-full min-h-0">
                    <div class="col-span-9 flex flex-col h-full min-h-0">
                        <div class="flex items-center justify-between mb-2 px-1">
                            <label class="text-[11px] font-bold text-slate-400 uppercase">Editor (HTML)</label>

                            <div class="flex items-center gap-1.5" id="editor-toolbar">
                                <button type="button" class="btn-toolbar" data-format="b" title="Bold">B</button>
                                <button type="button" class="btn-toolbar" data-format="i" title="Italic">I</button>
                                <button type="button" class="btn-toolbar" data-format="p" title="Paragraph">P</button>
                                <button type="button" class="btn-toolbar" data-format="h1" title="Heading 1">H1</button>
                                <button type="button" class="btn-toolbar" data-format="h2" title="Heading 2">H2</button>
                                <button type="button" class="btn-toolbar" data-format="ul"
                                    title="Unordered List">UL</button>
                                <button type="button" class="btn-toolbar" data-format="ol" title="Ordered List">OL</button>
                                <button type="button" class="btn-toolbar" data-format="br" title="Line Break">Br</button>
                                <button type="button" class="btn-toolbar" data-format="a"
                                    title="Insert Link">&lt;&gt;</button>
                            </div>
                        </div>

                        <div id="ace-editor"
                            class="flex-1 border border-slate-200 rounded-2xl overflow-hidden custom-scrollbar">
                        </div>
                    </div>

                    <div class="col-span-3 flex flex-col min-h-0 mt-3">
                        <label class="block text-center text-[11px] font-bold text-slate-400 uppercase ml-1 mb-2">Available
                            Banners</label>
                        <div id="imageStrip"
                            class="flex-1 flex flex-col gap-3 overflow-y-auto p-3 bg-slate-50 border border-slate-200 rounded-2xl custom-scrollbar items-center">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end items-center border-t border-slate-100 pt-4 shrink-0 bg-white">
                <button id="savePage" class="btn-primary h-10">Update Menu</button>
            </div>
        </div>
    </div>
@endsection