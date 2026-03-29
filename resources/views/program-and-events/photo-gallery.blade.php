@extends('layouts.app')
@section('title', $pageMenu->name)

@section('content')
    <div class="flex flex-col">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-12">
            @foreach($concerns as $concern)
                @php
                    $galleries = $concern->galleries;
                    $firstImg = $galleries->first();

                    $imageUrls = $galleries->map(function ($g) {
                        return asset('storage/' . $g->file_path);
                    })->toJson();

                    $thumbUrls = $galleries->map(function ($g) {
                        $base = basename($g->file_path, '.webp');
                        return asset('storage/' . dirname($g->file_path) . '/thumbs/' . $base . '-250.webp');
                    })->toJson();

                    $firstBase = basename($firstImg->file_path, '.webp');
                    $firstThumb = dirname($firstImg->file_path) . '/thumbs/' . $firstBase . '-700.webp';
                @endphp

                <div class="cursor-pointer group flex flex-col bg-white rounded-2xl overflow-hidden border border-slate-200 hover:border-orion-blue transition-colors duration-200"
                    onclick="openGalleryModal('{{ addslashes($concern->menu->name) }}', {{ $imageUrls }}, {{ $thumbUrls }})">

                    <div class="w-full aspect-20/9 overflow-hidden shimmer bg-white relative">
                        <img src="{{ asset('storage/' . $firstThumb) }}" class="w-full h-full object-cover"
                            onload="this.parentElement.classList.remove('shimmer')" alt="{{ $concern->menu->name }}">
                    </div>

                    <div class="p-4 flex items-center justify-between">
                        <span class="text-lg font-semibold text-slate-900">
                            {{ $concern->menu->name }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

    <div id="galleryModal"
        class="fixed inset-0 z-200 hidden bg-black overflow-hidden opacity-0 transition-opacity duration-300">

        <button onclick="closeGalleryModal()"
            class="group absolute top-2 right-2 z-220 w-9 h-9 rounded-full flex items-center justify-center transition-all duration-200 cursor-pointer hover:bg-white/50">

            <svg class="w-7 h-8 fill-white transition-colors" clip-rule="evenodd" fill-rule="evenodd"
                stroke-linejoin="round" stroke-miterlimit="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">

                <path
                    d="m12 10.93 5.719-5.72c.146-.146.339-.219.531-.219.404 0 .75.324.75.749 0 .193-.073.385-.219.532l-5.72 5.719 5.719 5.719c.147.147.22.339.22.531 0 .427-.349.75-.75.75-.192 0-.385-.073-.531-.219l-5.719-5.719-5.719 5.719c-.146.146-.339.219-.531.219-.401 0-.75-.323-.75-.75 0-.192.073-.384.22-.531l5.719-5.719-5.72-5.719c-.146-.147-.219-.339-.219-.532 0-.425.346-.749.75-.749.192 0 .385.073.531.219z" />

            </svg>

        </button>

        <div class="swiper mainGallerySwiper absolute inset-0 z-100">

            <div class="absolute inset-0 z-50 flex justify-center items-center pointer-events-none">
                <div class="relative w-full max-w-[calc(100vh*20/9)]">
                    <img id="galleryTitlePlaceholder" src=""
                        class="w-full h-auto opacity-0 invisible pointer-events-none select-none">

                    <span
                        class="gallery-slide-title absolute top-0 left-0 z-20 text-2xl text-white bg-black/50 px-4 py-2 font-semibold leading-tight block pointer-events-auto">
                    </span>
                </div>
            </div>

            <div class="swiper-wrapper" id="mainSwiperWrapper"></div>

            <div class="swiper-button-next text-white!"></div>
            <div class="swiper-button-prev text-white!"></div>

        </div>

        <div class="absolute bottom-0 left-0 w-full z-210 pb-6 pt-4 bg-linear-to-t from-black/80 to-transparent">

            <div class="px-6">
                <div class="swiper thumbsGallerySwiper w-full">
                    <div class="swiper-wrapper" id="thumbsSwiperWrapper"></div>

                    <div class="swiper-button-next thumbs-next"></div>
                    <div class="swiper-button-prev thumbs-prev"></div>
                </div>
            </div>
        </div>

    </div>

    <style>
        .mainGallerySwiper,
        .mainGallerySwiper .swiper-wrapper,
        .mainGallerySwiper .swiper-slide {
            height: 100%;
        }

        .thumbsGallerySwiper .swiper-slide {
            width: 200px;
            aspect-ratio: 20 / 9;
            flex-shrink: 0;
            opacity: 0.4;
            transition: all .25s ease;
            border: 2px solid transparent;
        }

        .thumbsGallerySwiper .swiper-slide-thumb-active {
            opacity: 1;
            border: 2px solid #fff;
        }

        .thumbsGallerySwiper .swiper-button-next,
        .thumbsGallerySwiper .swiper-button-prev {
            color: white;
            width: 34px;
            height: 34px;
        }
    </style>
@endsection