<section class="relative w-full pt-22.5">
    <div class="swiper homeSwiper w-full">
        <div class="swiper-wrapper">
            @foreach($sliders as $slider)
                <div class="swiper-slide relative aspect-23/9 overflow-hidden">
                    <img src="{{ asset($slider->image_path) }}" class="w-full h-full object-cover"
                        alt="{{ $slider->header_1 }}">

                    <div class="absolute inset-0 bg-linear-to-r from-black/75 to-transparent to-50% flex items-center">
                        <div class="container mx-auto w-[90%] max-w-350">
                            <div class="max-w-2xl text-white space-y-4 slide-content">
                                <span class="text-4xl md:text-6xl font-bold leading-tight">
                                    {{ $slider->header_1 }} <br>
                                    <span class="text-blue-400">{{ $slider->header_2 }}</span>
                                </span>
                                <p class="text-lg opacity-90">
                                    {{ $slider->description }}
                                </p>
                                @if($slider->link_url)
                                    <div class="pt-4">
                                        <a href="{{ url($slider->link_url) }}"
                                            class="bg-orion-blue hover:bg-white hover:text-orion-blue text-white px-8 py-3 rounded-full font-semibold transition-all duration-300 inline-block">
                                            {{ $slider->button_text }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="swiper-button-next text-white! after:text-2xl!"></div>
        <div class="swiper-button-prev text-white! after:text-2xl!"></div>

        <div class="swiper-pagination"></div>
    </div>
</section>