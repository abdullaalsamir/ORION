@if($homeConcerns->count() > 0)
    @php
        $groupedConcerns = $homeConcerns->groupBy(function($concern) {
            return $concern->menu->parent_id;
        });
    @endphp
    <section class="pb-16">

        <div class="relative">
            <div class="flex gap-4">

                <div class="flex-1 min-w-0">
                    <div class="swiper homeConcernsMainSwiper w-full aspect-20/9 rounded-r-xl overflow-hidden relative">
                        <div class="swiper-wrapper">
                            
                            @foreach($groupedConcerns as $parentId => $group)
                                <div class="swiper-slide w-full h-full relative">
                                    
                                    <div class="swiper homeConcernsChildSwiper w-full h-full">
                                        <div class="swiper-wrapper">
                                            @foreach($group as $concern)
                                                @php
                                                    $menu = $concern->menu;
                                                    $href = url($menu->full_slug);
                                                    $target = '_self';

                                                    if ($concern->is_redirect && !empty($concern->web_address)) {
                                                        $href = $concern->web_address;
                                                        $target = '_blank';
                                                    }
                                                @endphp

                                                <div class="swiper-slide w-full h-full relative group">
                                                    
                                                    <div class="w-full h-full shimmer bg-slate-100">
                                                        <img src="{{ asset('storage/' . $concern->cover_photo_path) }}" class="w-full h-full object-cover" alt="{{ $menu->name }}" onload="this.parentElement.classList.remove('shimmer')">
                                                    </div>

                                                    <div class="absolute inset-0 flex items-end pointer-events-none bg-[linear-gradient(to_top,rgba(0,0,0,0.8)_0%,rgba(0,0,0,0.6)_8%,rgba(0,0,0,0.4)_12%,rgba(0,0,0,0.2)_18%,transparent_30%)]">
                                                        <div class="p-6 w-full flex justify-between items-end pointer-events-auto z-10">
                                                            
                                                            <a href="{{ $href }}" target="{{ $target }}" class="inline-block hover:text-amber-300 transition-colors duration-300">
                                                                <h3 class="text-2xl font-bold text-white text-shadow-md">
                                                                    {{ $menu->name }}
                                                                </h3>
                                                            </a>
                                                            
                                                        </div>
                                                    </div>

                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        <div class="swiper-pagination bottom-2! z-20"></div>
                                    </div>
                                    
                                </div>
                            @endforeach

                        </div>

                        <div class="swiper-pagination main-swiper-pagination z-30"></div>

                    </div>
                </div>

                <div class="w-32 md:w-48 relative shrink-0">
                    <div class="absolute inset-0">
                        <div class="swiper homeConcernsThumbsSwiper w-full h-full overflow-hidden">
                            <div class="swiper-wrapper">

                                @foreach($groupedConcerns as $parentId => $group)
                                    @php 
                                        $parentMenu = $group->first()->menu->parent;
                                        $parentName = $parentMenu ? $parentMenu->name : $group->first()->menu->name;
                                    @endphp

                                    <div class="swiper-slide w-full h-auto! aspect-20/9 cursor-pointer rounded-lg overflow-hidden relative shimmer transition-all duration-300 border-2 border-transparent">
                                        
                                        <div class="absolute inset-0 flex">
                                            @foreach($group as $index => $concern)
                                                @php
                                                    $coverBase = basename($concern->cover_photo_path, '.webp');
                                                    $coverThumbPath = dirname($concern->cover_photo_path) . '/thumbs/' . $coverBase . '-200.webp';
                                                @endphp
                                                <div class="flex-1 h-full relative border-r border-white/20 last:border-r-0">
                                                    <img src="{{ asset('storage/' . $coverThumbPath) }}" class="w-full h-full object-cover" alt="strip" @if($index === 0) onload="this.closest('.shimmer')?.classList.remove('shimmer')" @endif>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="absolute inset-0 transition-all duration-300 thumb-overlay bg-slate-900/50 backdrop-blur-[1px]"></div>

                                        <div class="absolute inset-0 flex items-center justify-center p-2 z-10 pointer-events-none">
                                            <span class="font-bold text-sm text-center transition-colors duration-300 thumb-text text-white drop-shadow-md leading-tight">
                                                {{ $parentName }}
                                            </span>
                                        </div>

                                    </div>

                                @endforeach

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </section>

    <style>

        .homeConcernsThumbsSwiper .swiper-slide {
            border-color: #E2E8F0;
        }

        .homeConcernsThumbsSwiper .swiper-slide .thumb-overlay {
            background-color: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(1px);
        }

        .homeConcernsThumbsSwiper .swiper-slide .thumb-text {
            color: #08519e;
        }

        .homeConcernsThumbsSwiper .swiper-slide-thumb-active {
            border-color: #08519e;
        }

        .homeConcernsThumbsSwiper .swiper-slide-thumb-active .thumb-overlay {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(1px);
        }

        .homeConcernsThumbsSwiper .swiper-slide-thumb-active .thumb-text {
            color: #ffffff;
        }

        .homeConcernsChildSwiper .swiper-pagination-bullet {
            background: #ffffff;
            opacity: 0.5;
        }

        .homeConcernsChildSwiper .swiper-pagination-bullet-active {
            background: #08519e;
            opacity: 1;
        }

        .homeConcernsMainSwiper .main-swiper-pagination {
            position: absolute;
            right: 15px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: auto !important;
        }

        .homeConcernsMainSwiper .main-swiper-pagination .swiper-pagination-bullet {
            background: #ffffff;
            opacity: 0.5;
            width: 8px !important;
            height: 8px !important;
            margin: 0 !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
        }

        .homeConcernsMainSwiper .main-swiper-pagination .swiper-pagination-bullet-active {
            opacity: 1;
            background: #08519e;
        }

    </style>

@endif