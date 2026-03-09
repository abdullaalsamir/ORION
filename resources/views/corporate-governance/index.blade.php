@extends('layouts.app')

@section('content')
    @forelse($items->groupBy(fn($item) => $item->publication_date->format('Y')) as $year => $reports)
        <div class="mb-12 tr-slide-in">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-1.5 h-8 bg-orion-blue rounded-full"></div>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">{{ $year }}</h2>
                <div class="flex-1 h-px bg-linear-to-r from-orion-blue/40 to-transparent"></div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="divide-y divide-slate-100">
                    @foreach($reports as $index => $item)
                        <div class="group flex flex-col sm:flex-row items-center gap-6 px-6 py-4 transition-all duration-300">

                            <div class="flex items-center gap-5 shrink-0">
                                <span class="text-sm text-slate-300">
                                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                </span>
                                <div class="text-rose-500 flex items-center justify-center">
                                    <i class="fa-solid fa-file-pdf text-3xl"></i>
                                </div>
                            </div>

                            <div class="grow text-center sm:text-left">
                                <h3
                                    class="text-base font-semibold text-slate-700 group-hover:text-orion-blue transition-colors leading-snug">
                                    {{ $item->title }}
                                </h3>

                                @if($item->description)
                                    <p class="text-sm text-slate-500 mt-1">
                                        {{ $item->description }}
                                    </p>
                                @endif

                                <div class="mt-2 text-xs text-slate-400 uppercase tracking-wide">
                                    {{ $item->publication_date->format('d F, Y') }}
                                </div>
                            </div>

                            <div class="flex items-center gap-3 shrink-0">
                                <a href="{{ url($menu->full_slug . '/' . $item->filename) }}" target="_blank"
                                    class="flex items-center gap-2 px-4 py-2 rounded-lg bg-white text-orion-blue hover:bg-orion-blue hover:text-white font-bold text-xs transition-all duration-300 border border-slate-200 hover:border-orion-blue"
                                    title="View Report">
                                    <i class="fa-solid fa-eye text-[10px]"></i>
                                    View
                                </a>

                                <a href="{{ url($menu->full_slug . '/' . $item->filename) }}" download="{{ $item->title }}.pdf"
                                    class="w-9 h-9 rounded-lg bg-white text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-all duration-300 border border-slate-100"
                                    title="Download PDF">
                                    <i class="fa-solid fa-download text-xs"></i>
                                </a>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @empty
        <div class="p-24 text-center">
            <div class="relative inline-block mb-6">
                <i class="fa-solid fa-file-invoice text-slate-200 text-8xl"></i>
            </div>
            <h3 class="text-base font-bold text-slate-500 mb-2 uppercase tracking-wide">No Reports Found</h3>
            <p class="text-slate-400 font-medium">There Are Currently No Corporate Governance Available For Viewing.</p>
        </div>
    @endforelse
@endsection