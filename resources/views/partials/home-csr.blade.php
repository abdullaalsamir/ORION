@if(isset($csrItems) && $csrItems->isNotEmpty() && $csrMenu)
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-left text-orion-blue text-2xl uppercase font-bold">
            Corporate Social Responsibilities
        </h2>

        <a href="{{ url($csrMenu->full_slug) }}"
            class="text-slate-400 font-semibold uppercase text-sm tracking-wider hover:text-orion-blue mt-1">
            View All
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 overflow-hidden items-stretch">
        @foreach($csrItems as $item)
            <a href="{{ url($csrMenu->full_slug . '/' . $item->slug) }}"
                class="index-card group bg-white rounded-xl overflow-hidden flex flex-col">

                <div class="aspect-video overflow-hidden shimmer relative border-b border-slate-200">
                    <img src="{{ url($csrMenu->full_slug . '/' . basename($item->image_path)) }}"
                        class="product-image w-full h-full object-cover transition-transform duration-300"
                        alt="{{ $item->title }}">
                </div>

                <div class="p-6 flex flex-col grow">
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-3">
                        {{ $item->csr_date->format('d F, Y') }}
                    </span>
                    <h3
                        class="text-xl font-semibold text-slate-900 capitalize group-hover:text-orion-blue transition-all duraion-300 line-clamp-3 mb-3">
                        {{ $item->title }}
                    </h3>
                    <p class="text-slate-500 text-base leading-relaxed line-clamp-3">
                        {{ $item->description }}
                    </p>
                </div>
            </a>
        @endforeach
    </div>
@endif