@if(isset($homeProducts) && $homeProducts->isNotEmpty() && $productsMenu)
    <section class="mt-16">

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-left text-orion-blue text-2xl uppercase font-bold">
                New Products
            </h2>

            <a href="{{ url($productsMenu->full_slug) }}"
                class="text-slate-400 font-semibold uppercase text-sm tracking-wider hover:text-orion-blue mt-1">
                View All
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 overflow-hidden items-stretch">
            @foreach($homeProducts as $p)
                <a href="{{ url('products/' . $p->generic->slug . '/' . Str::slug($p->trade_name)) }}"
                    class="index-card group bg-white rounded-xl overflow-hidden">

                    <div class="aspect-video border-b border-slate-200 overflow-hidden shimmer relative">
                        <img src="{{ url('products/' . $p->generic->slug . '/' . basename($p->image_path)) }}"
                            class="product-image w-full h-full object-cover transition-transform duration-300"
                            alt="{{ $p->trade_name }}">
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

    </section>
@endif