@extends('layouts.app')

@section('title', $menu->name)
@section('meta_title', $menu->name)
@section('meta_description', 'Discover the Corporate Social Responsibility (CSR) initiatives and projects of ORION, making a positive impact on communities.')
@section('meta_image', asset('logo.svg'))

@section('content')

    <div class="{{ empty($menu->content) ? 'mt-0' : 'mt-8' }}">

        <div id="main-smooth-wrapper" class="smooth-container">
            <div id="csr-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 items-stretch">
                @foreach($items as $item)
                    <a href="{{ url($menu->full_slug . '/' . $item->slug) }}"
                        class="index-card group bg-white rounded-xl overflow-hidden flex flex-col">

                        <div class="aspect-video overflow-hidden shimmer relative border-b border-slate-200">
                            <img src="{{ asset('storage/' . $item->image_path) }}"
                                class="product-image w-full h-full object-cover transition-transform duration-700">
                        </div>

                        <div class="p-6 flex flex-col grow">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-3">
                                {{ $item->csr_date->format('d F, Y') }}
                            </span>

                            <h3
                                class="text-xl font-semibold text-slate-900 capitalize group-hover:text-orion-blue transition-all duration-300 line-clamp-3 mb-3">
                                {{ $item->title }}
                            </h3>

                            <span class="text-slate-500 text-base leading-relaxed line-clamp-3">
                                {!! $item->description !!}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>

            <div>
                {{ $items->links() }}
            </div>
        </div>

    </div>

@endsection