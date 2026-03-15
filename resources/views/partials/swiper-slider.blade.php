<section class="relative w-full pt-22.5">
    <div class="swiper homeSwiper w-full">
        <div class="swiper-wrapper">
            @foreach($sliders as $slider)
                <div class="swiper-slide relative aspect-23/9 overflow-hidden">
                    <img src="{{ asset($slider->image_path) }}" class="w-full h-full object-cover"
                        alt="{{ $slider->header_1 }}">

                    <div
                        class="absolute inset-0 flex items-center bg-[linear-gradient(to_right,rgba(0,0,0,0.75)_0%,rgba(0,0,0,0.5)_25%,rgba(0,0,0,0)_50%)]">
                        <div class="container mx-auto w-[90%] max-w-350">
                            <div class="max-w-3xl space-y-4 slide-content">
                                <span class="text-5xl text-white text-shadow-lg font-bold leading-tight">
                                    {{ $slider->header_1 }} <br>
                                    <span class="text-amber-300">{{ $slider->header_2 }}</span>
                                </span>
                                <p class="text-lg text-white opacity-90">
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

        <div class="swiper-button-next text-white!"></div>
        <div class="swiper-button-prev text-white!"></div>

        <div class="swiper-pagination"></div>
    </div>
</section>