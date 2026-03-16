@extends('layouts.app')

@section('title', $item->title)
@section('meta_title', $item->title)
@section('meta_description', Str::limit(strip_tags($item->description), 150))
@section('meta_image', asset('storage/' . $item->file_path))

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
        <nav class="flex items-center gap-2 text-sm font-medium text-slate-500">
            <a href="{{ url('/') }}" class="hover:text-orion-blue">Home</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <a href="{{ url($menu->full_slug) }}" class="hover:text-orion-blue">News List</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="capitalize line-clamp-1">{{ $item->title }}</span>
        </nav>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        <article class="lg:col-span-8">
            <div class="rounded-2xl overflow-hidden mb-8 aspect-video shimmer relative border border-slate-200">
                <img src="{{ asset('storage/' . $item->file_path) }}" alt="{{ $item->title }}"
                    class="product-image w-full h-full object-cover">
            </div>

            <div class="flex items-center gap-2 text-slate-400 font-semibold uppercase tracking-wider text-xs mb-4">
                {{ $item->news_date->format('d F, Y') }}
            </div>

            <div class="text-2xl font-bold text-orion-blue leading-tight capitalize mb-4">
                {{ $item->title }}
            </div>

            <div class="prose prose-lg max-w-none text-slate-700 leading-relaxed text-justify">
                {!! $item->description !!}
            </div>

            @php
                $shareUrl = urlencode(request()->fullUrl());
                $shareTitle = urlencode($item->title);
            @endphp

            <div class="mt-8 pt-8 border-t border-slate-200 flex items-center gap-4">
                <span class="font-semibold text-slate-500">Share this news:</span>
                <div class="flex gap-2">

                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank"
                        rel="noopener noreferrer"
                        class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center hover:bg-orion-blue hover:text-white hover:border-orion-blue transition-all text-slate-500 border border-slate-200">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>

                    <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}" target="_blank"
                        rel="noopener noreferrer"
                        class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center hover:bg-orion-blue hover:text-white hover:border-orion-blue transition-all text-slate-500 border border-slate-200">
                        <i class="fa-brands fa-x-twitter"></i>
                    </a>

                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}" target="_blank"
                        rel="noopener noreferrer"
                        class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center hover:bg-orion-blue hover:text-white hover:border-orion-blue transition-all text-slate-500 border border-slate-200">
                        <i class="fa-brands fa-linkedin-in"></i>
                    </a>

                    <a href="https://t.me/share/url?url={{ $shareUrl }}&text={{ $shareTitle }}" target="_blank"
                        rel="noopener noreferrer"
                        class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center hover:bg-orion-blue hover:text-white hover:border-orion-blue transition-all text-slate-500 border border-slate-200">
                        <i class="fa-brands fa-telegram"></i>
                    </a>

                    <a href="https://api.whatsapp.com/send?text={{ $shareTitle }}%20{{ $shareUrl }}" target="_blank"
                        rel="noopener noreferrer"
                        class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center hover:bg-orion-blue hover:text-white hover:border-orion-blue transition-all text-slate-500 border border-slate-200">
                        <i class="fa-brands fa-whatsapp"></i>
                    </a>

                </div>
            </div>
        </article>

        <aside class="lg:col-span-4">
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden sticky top-27.5">
                <div class="p-6 border-b border-slate-200 bg-slate-50/50">
                    <span class="text-xl font-bold text-slate-700 flex items-center gap-3">
                        <span class="flex-none w-1 h-5 bg-orion-blue rounded-full"></span>
                        Recent News
                    </span>
                </div>

                <div class="divide-y divide-slate-200">
                    @foreach($related as $rel)
                        @php
                            $isPdf = $rel->file_type === 'pdf';
                            $link = $isPdf ? asset('storage/' . $rel->file_path) : url($menu->full_slug . '/' . $rel->slug);
                        @endphp

                        <a href="{{ $link }}" @if($isPdf) target="_blank" rel="noopener noreferrer" @endif
                            class="group flex p-5 hover:bg-blue-50 transition-colors">

                            @if(!$isPdf)
                                <div class="w-20 h-14 rounded-md overflow-hidden shrink-0 shimmer relative border border-slate-200">
                                    <img src="{{ asset('storage/' . $rel->file_path) }}"
                                        class="product-image w-full h-full object-cover transition-transform duration-500">
                                </div>
                            @else
                                <div
                                    class="w-20 h-14 rounded-md overflow-hidden bg-red-50 shrink-0 flex items-center justify-center border border-red-100 transition-colors group-hover:bg-red-100">
                                    <i class="fas fa-file-pdf text-xl text-red-500"></i>
                                </div>
                            @endif

                            <div class="ml-4">
                                <span
                                    class="text-sm font-semibold text-slate-700 line-clamp-2 group-hover:text-orion-blue transition-colors leading-snug -mt-0.75 mb-1">
                                    {{ $rel->title }}
                                </span>
                                <div class="flex items-center gap-2 text-xs text-slate-400 uppercase tracking-wider">
                                    {{ $rel->news_date->format('d F, Y') }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="p-4 bg-slate-50/50 border-t border-slate-200">
                    <a href="{{ url($menu->full_slug) }}"
                        class="flex items-center justify-center gap-2 w-full py-3 bg-white text-orion-blue text-xs font-bold uppercase tracking-wider rounded-xl border border-slate-200 hover:border-orion-blue hover:bg-orion-blue hover:text-white transition-all duration-300">
                        View All News
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                </div>
            </div>
        </aside>
    </div>
@endsection