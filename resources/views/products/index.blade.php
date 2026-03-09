@extends('layouts.app')

@section('content')
    <div class="mb-10 sticky z-80">
        <div class="flex flex-wrap items-center justify-between gap-6">
            <div class="flex bg-slate-100 p-1 rounded-xl border border-slate-200">
                <button onclick="setFilterMode('generic')" id="mode-generic"
                    class="filter-mode-btn active px-6 py-2 rounded-lg text-xs font-bold cursor-pointer transition-all">
                    Generic
                </button>
                <button onclick="setFilterMode('trade')" id="mode-trade"
                    class="filter-mode-btn px-6 py-2 rounded-lg text-xs font-bold cursor-pointer transition-all">
                    Trade
                </button>
            </div>

            <div class="flex items-center gap-1 overflow-x-auto pb-2 md:pb-0 alphabet-container">
                <button onclick="setLetter('all', event)"
                    class="letter-btn text-xs border border-slate-200 cursor-pointer active">All</button>
                @foreach(range('A', 'Z') as $char)
                    <button onclick="setLetter('{{ $char }}', event)"
                        class="letter-btn text-xs border border-slate-200 cursor-pointer">{{ $char }}</button>
                @endforeach
            </div>
        </div>
    </div>

    <div id="main-smooth-wrapper" class="smooth-container">
        <div id="product-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 items-stretch">
            @foreach($products as $p)
                <a href="{{ url('products/' . $p->generic->slug . '/' . $p->slug) }}"
                    class="index-card group bg-white rounded-xl overflow-hidden"
                    data-generic="{{ strtoupper($p->generic->name) }}" data-trade="{{ strtoupper($p->trade_name) }}">

                    <div class="aspect-video border-b border-slate-200 overflow-hidden shimmer relative">
                        <img src="{{ url('products/' . $p->generic->slug . '/' . basename($p->image_path)) }}"
                            class="product-image w-full h-full object-cover">
                    </div>

                    <div class="p-6 flex flex-col grow">
                        <span class="text-xs font-semibold text-orion-blue uppercase tracking-wider block mb-3">
                            {{ $p->generic->name }}
                        </span>
                        <h3 class="text-xl font-semibold text-slate-900">
                            {{ $p->trade_name }}
                        </h3>
                    </div>
                </a>
            @endforeach
        </div>

        <div id="no-results" class="hidden py-20 text-center">
            <i class="fa-solid fa-box-open text-6xl text-slate-200 mb-4"></i>
            <p class="text-slate-500 font-medium">No products found matching your selection.</p>
        </div>
    </div>
    <div>
        {{ $products->links() }}
    </div>
@endsection