@extends('layouts.app')

@section('content')
    <div id="director-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 items-stretch">
        @foreach($items as $item)
            <a href="{{ url($menu->full_slug . '/' . $item->slug) }}"
                class="index-card group bg-white rounded-xl overflow-hidden flex flex-col w-full">

                <div class="aspect-3/4 overflow-hidden shimmer relative border-b border-slate-200">
                    <img src="{{ url($menu->full_slug . '/' . basename($item->image_path)) }}"
                        class="product-image w-full h-full object-cover transition-transform duration-700">
                </div>

                <div class="px-6 py-4 flex flex-col grow text-left">
                    <span class="text-xl font-semibold text-slate-900">
                        {{ $item->name }}
                    </span>
                    <p class="text-xs font-semibold text-orion-blue uppercase tracking-wider block mt-2">
                        {{ $item->designation }}
                    </p>
                </div>
            </a>
        @endforeach
    </div>
@endsection