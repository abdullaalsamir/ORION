@extends('layouts.app')
@section('title', $pageMenu->name)

@php
    $hasSlider = $concern && $concern->galleries->count() > 0;
    $hasDescription = ($concern && !empty($concern->description)) || !empty($pageMenu->content);

    $mainPadding =
        ($hasSlider && $hasDescription) ? 'pb-10' :
        ($hasDescription ? 'pb-16' : 'pb-0');
@endphp

@section('main_pb', $mainPadding)

@section('fullwidth')
    @if($hasSlider)
        <div class="relative w-full {{ $hasDescription ? 'mb-10' : 'mb-0' }} overflow-hidden aspect-23/9">
            <div class="swiper businessesSwiper w-full h-full">

                <div class="swiper-wrapper">
                    @foreach($concern->galleries as $gallery)
                        <div class="swiper-slide w-full h-full relative">
                            <img src="{{ url($pageMenu->full_slug . '/' . basename($gallery->file_path)) }}"
                                class="w-full h-full object-cover" alt="{{ $pageMenu->name }}">
                        </div>
                    @endforeach
                </div>

                <div
                    class="absolute inset-0 z-10 pointer-events-none flex items-center bg-[linear-gradient(to_right,rgba(0,0,0,0.75)_0%,rgba(0,0,0,0.4)_30%,rgba(0,0,0,0)_60%)]">
                    <div class="container mx-auto w-[90%] max-w-350">
                        <span class="text-5xl text-white text-shadow-lg font-bold leading-tight max-w-lg block">
                            {{ $pageMenu->name }}
                        </span>
                    </div>
                </div>

                <div class="swiper-button-next text-white!"></div>
                <div class="swiper-button-prev text-white!"></div>
                <div class="swiper-pagination"></div>

            </div>
        </div>
    @endif
@endsection

@section('content')

    <div class="flex flex-col">

        @if(!$hasSlider)
            <h1>
                <span class="px-4">
                    {{ $pageMenu->name }}
                </span>
            </h1>
        @endif

        @if($concern && !empty($concern->description))
            <div class="page-content prose max-w-none text-slate-700 text-justify leading-relaxed">
                {!! $concern->description !!}
            </div>
        @elseif(!empty($pageMenu->content))
            <div class="page-content prose max-w-none text-slate-700 text-justify leading-relaxed">
                {!! $pageMenu->content !!}
            </div>
        @endif

    </div>

@endsection