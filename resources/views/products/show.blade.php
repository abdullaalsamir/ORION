@extends('layouts.app')

@section('content')
    <div class="pb-10">
        <nav class="flex items-center gap-2 text-sm font-medium text-slate-500 mb-8">
            <a href="{{ url('/') }}" class="hover:text-orion-blue">Home</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <a href="{{ url($menu->full_slug) }}" class="hover:text-orion-blue">Products</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="capitalize line-clamp-1">{{ $product->trade_name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-4">
                <div class="sticky top-27.5">
                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden space-y-6">
                        <div class="aspect-video mb-0 border-b border-slate-200 shimmer relative">
                            <img src="{{ url('products/' . $product->generic->slug . '/' . basename($product->image_path)) }}"
                                alt="{{ $product->trade_name }}" class="product-image w-full h-full object-cover">
                        </div>
                        <div class="px-8 pt-4 pb-8">
                            <h2
                                class="text-left! uppercase text-xl font-bold text-orion-blue border-b border-slate-200 mb-4 pb-2">
                                {{ $product->trade_name }}
                            </h2>
                            <div class="space-y-4">
                                <div class="flex items-start gap-3">
                                    <div>
                                        <span
                                            class="block text-sm uppercase font-semibold text-slate-400 tracking-wider text-justify">Generic
                                            Name</span>
                                        <span
                                            class="text-base font-semibold text-slate-700 text-justify">{{ $product->generic->name }}</span>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div>
                                        <span
                                            class="block text-sm uppercase font-semibold text-slate-400 tracking-wider text-justify mt-2">Preparation</span>
                                        <span class="text-base text-slate-700 text-justify">
                                            {!! nl2br($product->preparation ?? 'N/A') !!}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div>
                                        <span
                                            class="block text-sm uppercase font-semibold text-slate-400 tracking-wider text-justify mt-2">Therapeutic
                                            Class</span>
                                        <span class="text-base text-slate-700 text-justify">
                                            {!! nl2br($product->therapeutic_class ?? 'N/A') !!}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-8">
                <div id="show-smooth-wrapper"
                    class="bg-white rounded-xl border border-slate-200 overflow-hidden smooth-container">
                    @php
                        $groups = [
                            'Clinical' => ['indications' => 'Indications', 'dosage_admin' => 'Dosage & Administration', 'contraindications' => 'Contraindications', 'overdosage' => 'Overdosage'],
                            'Safety' => ['precautions' => 'Precautions', 'side_effects' => 'Side Effects', 'drug_interactions' => 'Drug Interactions', 'use_children' => 'Use in Children', 'use_pregnancy_lactation' => 'Use in Pregnancy & Lactation', 'high_risk' => 'High Risk Groups'],
                            'Commercial' => ['presentation' => 'Presentation', 'how_supplied' => 'How Supplied', 'commercial_pack' => 'Commercial Pack', 'packaging' => 'Packaging', 'storage' => 'Storage', 'official_specification' => 'Official Specification']
                        ];
                    @endphp

                    <div class="flex border-b border-slate-200 overflow-x-auto bg-slate-50/50">
                        @foreach($groups as $groupName => $fields)
                            <button onclick="switchTab('{{ $groupName }}')" id="tab-btn-{{ $groupName }}"
                                class="tab-btn px-8 py-5 text-sm font-bold uppercase tracking-wider transition-all text-slate-400 {{ $loop->first ? 'active' : '' }}">
                                {{ $groupName }}
                            </button>
                        @endforeach
                    </div>

                    <div class="p-8">
                        @foreach($groups as $groupName => $fields)
                            <div id="tab-content-{{ $groupName }}"
                                class="tab-pane {{ $loop->first ? '' : 'hidden' }} space-y-10">
                                @foreach($fields as $key => $label)
                                    @if(!empty($product->$key))
                                        <div class="relative">
                                            <h4 class="text-orion-blue font-bold uppercase text-sm tracking-wider mb-1">{{ $label }}
                                            </h4>
                                            <div class="product-key prose prose-slate text-justify max-w-none">
                                                {!! $product->$key !!}
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection