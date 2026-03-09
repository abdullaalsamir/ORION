@extends('admin.layouts.app')
@section('title', 'Product Management')

@section('content')
    @php
        $col1 = [
            'indications' => 'Indications',
            'dosage_admin' => 'Dosage & Administration',
            'contraindications' => 'Contraindications',
            'overdosage' => 'Overdosage'
        ];

        $col2 = [
            'precautions' => 'Precautions',
            'side_effects' => 'Side Effects',
            'drug_interactions' => 'Drug Interactions',
            'use_children' => 'Use in Children',
            'use_pregnancy_lactation' => 'Pregnancy & Lactation',
            'high_risk' => 'High Risk Groups'
        ];

        $col3 = [
            'presentation' => 'Presentation',
            'how_supplied' => 'How Supplied',
            'commercial_pack' => 'Commercial Pack',
            'packaging' => 'Packaging',
            'storage' => 'Storage',
            'official_specification' => 'Official Specification'
        ];
    @endphp

    <div class="flex gap-6 h-full overflow-hidden">
        <aside class="w-84 bg-white rounded-2xl border border-slate-200 flex flex-col overflow-hidden">
            <div class="admin-card-header p-5!">
                <div class="flex flex-col">
                    <h1>Generics</h1>
                    <p class="text-xs text-slate-400">Drug Categories</p>
                </div>
                <button onclick="openGenericModal()"
                    class="btn-success w-8! h-8! p-0! flex items-center justify-center rounded-lg">
                    <i class="fas fa-plus text-xs"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-2 custom-scrollbar bg-slate-50/30">
                @foreach($generics as $g)
                    <div class="generic-list-item p-3 bg-white border border-slate-200 rounded-2xl hover:border-admin-blue/50 cursor-pointer transition-all group"
                        onclick="loadProducts({{ $g->id }}, this)">
                        <div class="flex items-center justify-between gap-3">
                            <span class="generic-name font-bold text-slate-700 text-sm whitespace-normal leading-tight flex-1">
                                {{ $g->name }}
                            </span>

                            <div class="flex flex-col items-end gap-1.5 shrink-0">
                                <span class="badge {{ $g->is_active ? 'badge-success' : 'badge-danger' }} text-[9px]! w-fit">
                                    {{ $g->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                <div class="flex items-center gap-1">
                                    <button class="btn-icon p-1.5! bg-slate-50! border border-slate-100"
                                        onclick="event.stopPropagation(); openEditGeneric({{ json_encode($g) }})">
                                        <i class="fas fa-pencil text-xs"></i>
                                    </button>
                                    <button class="btn-danger p-1.5! bg-slate-50! border border-slate-100"
                                        onclick="event.stopPropagation(); deleteGeneric({{ $g->id }})">
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                @if($archivedCount > 0)
                    <div class="generic-list-item archived-item p-3 bg-red-50/50 border border-red-100 rounded-2xl hover:border-red-400 cursor-pointer transition-all group mt-4"
                        onclick="loadProducts(0, this)">
                        <div class="flex items-center justify-between gap-3">
                            <span class="generic-name font-bold text-red-500 text-sm whitespace-normal leading-tight flex-1">
                                Archived Products
                            </span>
                            <span
                                class="badge bg-red-100! text-red-500! border-red-200! text-[8px]!">{{ $archivedCount }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </aside>

        <main class="flex-1 admin-card" id="productArea">
            <div class="flex flex-col items-center justify-center h-full text-slate-300 gap-4">
                <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center text-3xl"><i
                        class="fas fa-capsules"></i></div>
                <div class="text-center">
                    <h2 class="text-slate-400!">No Generic Selected</h2>
                    <p class="text-xs">Choose a Generic from the left to manage products.</p>
                </div>
            </div>
        </main>
    </div>

    <div id="genericModal" class="modal-overlay hidden">
        <div class="modal-content max-w-md!">
            <div class="flex justify-between items-center mb-6 pb-3 border-b border-slate-100">
                <h1 id="genTitle" class="mb-0!">Add Generic</h1>
                <button onclick="closeModal('genericModal')" class="btn-icon"><i class="fas fa-times text-xl"></i></button>
            </div>

            <form id="genForm">
                @csrf
                <div class="flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Generic Name</label>
                    <input type="text" name="name" id="genName" required class="input-field w-full"
                        placeholder="Enter generic name">
                    <span id="genNameError" class="text-[10px] text-red-500 font-bold uppercase ml-1 mt-1 hidden"></span>
                </div>

                <div class="mt-8 border-slate-100 flex items-center justify-between">
                    <div id="genActiveWrapper" class="hidden">
                        <label class="toggle-switch">
                            <input type="checkbox" id="genActive" name="is_active">
                            <div class="toggle-bg"></div>
                            <span id="genStatusLabel" class="ml-3 font-bold text-slate-600 text-sm">Active</span>
                        </label>
                    </div>

                    <div class="ml-auto">
                        <button type="submit" id="genSubmitBtn" class="btn-primary h-10">Save Generic</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="productModal" class="modal-overlay hidden">
        <div class="modal-content max-w-[95vw]! h-[90vh]! flex flex-col">
            <div class="flex justify-between items-center mb-6 pb-3 border-b border-slate-100 shrink-0">
                <h1 id="prodTitle" class="mb-0!">Add Product</h1>
                <div class="flex items-center">
                    <button type="button" onclick="closeModal('productModal')" class="btn-icon ml-2">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <form id="prodForm" class="flex-1 overflow-y-auto custom-scrollbar pr-2 space-y-6">
                @csrf

                <div class="flex gap-8 px-2 items-start flex-wrap lg:flex-nowrap">
                    <div class="flex flex-col gap-1 w-lg shrink-0">
                        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1 block">Product Image</label>
                        <input type="file" name="image" id="prodInput" accept="image/*" class="hidden"
                            onchange="handlePreview(this, 'prodPreview')">
                        <div class="relative group cursor-pointer" onclick="document.getElementById('prodInput').click()">
                            <div class="aspect-video bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center overflow-hidden transition-all group-hover:border-admin-blue"
                                id="prodPreview">
                                <i class="fas fa-cloud-arrow-up text-2xl text-slate-300 mb-2"></i>
                                <span
                                    class="text-slate-400 font-bold text-[10px] uppercase tracking-widest text-center px-4">Select
                                    Image</span>
                            </div>
                            <div id="prodReplaceOverlay"
                                class="absolute inset-0 bg-admin-blue/60 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all rounded-2xl">
                                <span class="text-white font-bold text-xs uppercase tracking-widest">Click to Replace
                                    Image</span>
                            </div>
                        </div>
                        <span id="prodImageError"
                            class="text-[10px] text-red-500 font-bold uppercase ml-1 mt-1 hidden">Please select a product
                            image</span>
                    </div>

                    <div class="flex-1 flex flex-col gap-4">
                        <div class="flex flex-col gap-1">
                            <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Trade Name</label>
                            <input type="text" name="trade_name" id="p_trade_name" required class="input-field w-full">
                            <span id="prodNameError"
                                class="text-[10px] text-red-500 font-bold uppercase ml-1 mt-1 hidden"></span>
                        </div>

                        <div class="flex-col gap-1 hidden" id="p_generic_id_wrapper">
                            <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Assign Generic
                                (Optional)</label>
                            <select name="generic_id" id="p_generic_id" class="input-field w-full">
                                <option value="">⁝⁝⁝ No Generic / Archived ⁝⁝⁝</option>
                                @foreach($generics as $g)
                                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Preparation</label>
                            <textarea name="preparation" id="p_preparation"
                                class="input-field w-full h-22 py-2 resize-none custom-scrollbar"></textarea>
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Therapeutic Class</label>
                            <textarea name="therapeutic_class" id="p_therapeutic_class"
                                class="input-field w-full h-22 py-2 resize-none custom-scrollbar"></textarea>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-8 mt-6 pt-6 px-2">

                    <div class="space-y-6">
                        @foreach($col1 as $key => $label)
                            <div class="flex flex-col gap-1">
                                <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">{{ $label }}</label>
                                <textarea name="{{ $key }}" id="p_{{ $key }}"
                                    class="input-field w-full h-32 py-3 resize-none"></textarea>
                            </div>
                        @endforeach
                    </div>

                    <div class="space-y-6">
                        @foreach($col2 as $key => $label)
                            <div class="flex flex-col gap-1">
                                <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">{{ $label }}</label>
                                <textarea name="{{ $key }}" id="p_{{ $key }}"
                                    class="input-field w-full h-32 py-3 resize-none"></textarea>
                            </div>
                        @endforeach
                    </div>

                    <div class="space-y-6">
                        @foreach($col3 as $key => $label)
                            <div class="flex flex-col gap-1">
                                <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">{{ $label }}</label>
                                <textarea name="{{ $key }}" id="p_{{ $key }}"
                                    class="input-field w-full h-32 py-3 resize-none"></textarea>
                            </div>
                        @endforeach
                    </div>

                </div>

                <div
                    class="flex items-center justify-between mt-4 sticky bottom-0 bg-white pb-2 pt-4 border-t border-slate-50 z-10">
                    <div id="prodActiveWrapper" class="opacity-0 pointer-events-none">
                        <label class="toggle-switch">
                            <input type="checkbox" id="p_active" name="is_active">
                            <div class="toggle-bg"></div>
                            <span id="prodStatusLabel" class="ml-3 font-bold text-slate-600 text-sm">Active</span>
                        </label>
                    </div>
                    <button type="submit" id="prodSubmitBtn" class="btn h-10"></button>
                </div>
            </form>
        </div>
    </div>
@endsection