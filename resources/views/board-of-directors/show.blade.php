@extends('layouts.app')

@section('content')
    <nav class="flex items-center gap-2 text-sm font-medium text-slate-500 mb-8">
        <a href="{{ url('/') }}" class="hover:text-orion-blue">Home</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <a href="{{ url($menu->full_slug) }}" class="hover:text-orion-blue">Board of Directors</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="capitalize line-clamp-1">{{ $item->name }}</span>
    </nav>

    <div class="overflow-hidden">

        <div class="float-left mr-8 mb-6 w-full md:w-1/3 lg:w-1/4 max-w-75">
            <div class="aspect-3/4 rounded-xl overflow-hidden shimmer relative border border-slate-200">
                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}"
                    class="product-image w-full h-full object-cover">
            </div>
        </div>

        <div class="text-content">
            <h3
                class="flex-none! text-left! !before:content-none !after:content-none my-0! text-2xl font-bold text-orion-blue leading-tight">
                {{ $item->name }}
            </h3>

            <p class="text-base font-bold text-slate-500 uppercase tracking-wider mt-1 mb-8">
                {{ $item->designation }}
            </p>

            <div class="prose prose-slate max-w-none text-slate-700 leading-relaxed text-justify">
                {!! $item->description !!}
            </div>
        </div>

    </div>
@endsection