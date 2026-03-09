@php
    $footer = \App\Models\Footer::find(1) ?? new \App\Models\Footer();

    $quickLinks = collect($footer->quick_links ?? [])
        ->map(function ($item) {
            if (empty($item['menu_id'])) {
                return null;
            }

            if ($item['menu_id'] === 'career') {
                return (object) ['name' => 'Career', 'full_slug' => 'career'];
            }

            if ($item['menu_id'] === 'sitemap') {
                return (object) ['name' => 'Sitemap', 'full_slug' => 'sitemap'];
            }

            return \App\Models\Menu::find($item['menu_id']);
        })
        ->filter()
        ->values();

    $socialLinks = collect($footer->social_links ?? []);
@endphp

<footer class="bg-orion-blue text-gray-100 pt-8 pb-2.5">
    <div class="container mx-auto w-[90%] max-w-350">
        <div class="grid grid-cols-1 lg:grid-cols-20 gap-12 mb-8">

            <div class="lg:col-span-11">
                <h1 class="border-b-2 border-white/30 pb-4">Contact Us</h1>
                <div class="flex flex-col md:flex-row">
                    <div class="flex-1 space-y-5">

                        <div class="space-y-1 opacity-90 min-h-7.5">
                            <h1 class="normal-case! tracking-normal mb-2">{{ $footer->company ?? '' }}</h1>
                        </div>

                        <div class="flex items-start gap-4 opacity-90 hover:opacity-100 transition-opacity group">
                            <i class="fa-solid fa-location-dot text-white text-sm mt-1 w-5 text-center"></i>
                            <p class="text-[15px] leading-relaxed">
                                {!! $footer->address_1 ? $footer->address_1 . '<br>' : '' !!}
                                {!! $footer->address_2 ? $footer->address_2 . '<br>' : '' !!}
                                {{ $footer->address_3 ?? '' }}
                            </p>
                        </div>

                        <div class="flex items-start gap-4 opacity-90 hover:opacity-100 transition-opacity group">
                            <i class="fa-solid fa-phone text-white text-sm mt-1 w-5 text-center"></i>
                            <div class="text-[15px]">
                                <p>{{ $footer->phone_1 ?? '' }}</p>
                                <p>{{ $footer->phone_2 ?? '' }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 opacity-90 hover:opacity-100 transition-opacity group">
                            <i class="fa-solid fa-fax text-white text-sm mt-1 w-5 text-center"></i>
                            <p class="text-[15px]">{{ $footer->fax ?? '' }}</p>
                        </div>

                        <div class="flex items-start gap-4 opacity-90 hover:opacity-100 transition-opacity group">
                            <i class="fa-solid fa-envelope text-white text-sm mt-1 w-5 text-center"></i>
                            <div class="text-[15px]">
                                @if($footer->email)
                                    <a href="mailto:{{ $footer->email }}"
                                        class="text-[15px] hover:text-white transition-all">{{ $footer->email }}</a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex-1 min-h-68 rounded-lg overflow-hidden border border-white/10 opacity-90 mt-1">
                        @if($footer->map_url)
                            <iframe src="{{ $footer->map_url }}" width="100%" height="100%" style="border:0;"
                                allowfullscreen="" loading="lazy"></iframe>
                        @else
                            <div
                                class="w-full h-full bg-slate-800 flex items-center justify-center text-slate-500 italic text-sm">
                                Map not configured</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 lg:pl-8">
                <h1 class="border-b-2 border-white/30 pb-4">Quick Links</h1>
                <ul class="space-y-[11.5px]">
                    @foreach($quickLinks as $m)
                        <li><a href="{{ url($m->full_slug) }}"
                                class="text-[15px] opacity-90 hover:opacity-100 hover:translate-x-1 transition-all inline-block hover:text-white">{{ $m->name }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="lg:col-span-4">
                <h1 class="border-b-2 border-white/30 pb-4">Follow Us</h1>
                <p class="text-[15px] mb-6 text-gray-200 opacity-90">{{ $footer->follow_us_desc ?? '' }}</p>
                <div class="flex flex-wrap gap-4">
                    @foreach($socialLinks as $social)
                        @if(!empty($social['url']))
                            <a href="{{ $social['url'] }}" target="_blank"
                                class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center hover:bg-white hover:text-orion-blue transition-all group shadow-sm hover:shadow-lg">
                                <i
                                    class="{{ $social['icon'] === 'fa-globe' ? 'fas' : 'fab' }} {{ $social['icon'] }} text-[15px]"></i>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <div class="pt-2.5 border-t border-white/30">
            <div class="text-center">
                <p class="text-sm text-gray-300 leading-relaxed">
                    Copyright &copy; {{ date('Y') }}
                    <span class="text-white font-semibold tracking-wide opacity-90">ORION</span>. All Rights Reserved.
                    <span class="block md:inline mt-1 md:mt-0">Design & Developed by:
                        <span class="text-white font-medium opacity-90">Information Technology (IT), ORION.</span>
                    </span>
                </p>
            </div>
        </div>
    </div>
</footer>