<section class="relative w-full pt-22.5">
    <div class="relative w-full aspect-23/9 overflow-hidden bg-slate-100">
        <div class="swiper homeSwiper w-full h-full absolute inset-0">

            <div class="swiper-wrapper h-full flex">
                @foreach($sliders as $slider)

                    <div class="swiper-slide w-full h-full relative shrink-0">

                        <!-- <div class="absolute inset-0 w-full h-full shimmer bg-slate-100">
                                <img src="{{ asset('storage/' . $slider->image_path) }}"
                                    class="w-full h-full object-cover opacity-0 transition-opacity duration-500"
                                    alt="{{ $slider->header_1 }}"
                                    onload="this.classList.remove('opacity-0'); this.parentElement.classList.remove('shimmer')">
                            </div> -->

                        <div class="absolute inset-0 w-full h-full">
                            <img src="{{ asset('storage/' . $slider->image_path) }}" class="w-full h-full object-cover"
                                alt="{{ $slider->header_1 }}">
                        </div>

                        <div
                            class="absolute inset-0 flex items-center bg-[linear-gradient(to_right,rgba(0,0,0,0.75)_0%,rgba(0,0,0,0.65)_6%,rgba(0,0,0,0.55)_14%,rgba(0,0,0,0.42)_22%,rgba(0,0,0,0.30)_30%,rgba(0,0,0,0.20)_40%,rgba(0,0,0,0.11)_48%,rgba(0,0,0,0.05)_55%,rgba(0,0,0,0)_60%)] pointer-events-none">
                            <div class="container pl-[5%] pointer-events-auto">
                                <div class="slide-content space-y-4">
                                    <span
                                        class="max-w-3xl text-5xl text-white text-shadow-lg font-bold leading-tight block">
                                        {{ $slider->header_1 }} <br>
                                        <span class="text-amber-300">{{ $slider->header_2 }}</span>
                                    </span>
                                    @if(!empty($slider->description))
                                        <p class="max-w-xl text-lg font-normal text-white text-shadow-xs mt-4">
                                            {{ $slider->description }}
                                        </p>
                                    @endif
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
    </div>
</section>