@extends('layouts.app')

@section('content')
    <div class="medical-journals-page"
        x-data="{ selectedYear: null, selectYear(year) { const wrapper = '#journals-smooth-wrapper'; animateHeight(wrapper, () => { this.selectedYear = year }); this.$nextTick(() => animateHeight(wrapper, () => {})); }, clearYear() { const wrapper = '#journals-smooth-wrapper'; animateHeight(wrapper, () => { this.selectedYear = null }); this.$nextTick(() => animateHeight(wrapper, () => {})); } }">

        <div id="journals-smooth-wrapper" class="smooth-container">

            <div x-show="!selectedYear"
                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 xl:grid-cols-7 gap-6 tr-slide-in pt-2">

                @foreach($groupedJournals as $year => $medicalJournals)
                    <button @click="selectYear({{ $year }})"
                        class="group aspect-square w-full border border-slate-200 rounded-2xl bg-white transition-all duration-300 hover:border-orion-blue hover:-translate-y-0.5 cursor-pointer overflow-hidden">

                        <div class="w-full h-full p-4 flex flex-col items-center justify-center">

                            <svg class="w-16 h-16 mt-1 mb-2" viewBox="0 0 380.28 384" xmlns="http://www.w3.org/2000/svg">
                                <g>
                                    <g>
                                        <rect x="257.83" y="0" width="122.45" height="384" rx="21.91" ry="21.91"
                                            fill="#fbbe66" />
                                        <rect x="257.83" y="0" width="95.85" height="384" rx="21.91" ry="21.91"
                                            fill="#fdcb82" />
                                    </g>
                                    <g>
                                        <circle cx="319.06" cy="312.79" r="34.57" fill="#fff" />
                                        <circle cx="319.06" cy="312.79" r="15.53" fill="#3c5959" />
                                    </g>
                                    <g>
                                        <rect x="284.49" y="25.94" width="69.13" height="159.59" rx="9.29" ry="9.29"
                                            fill="#fff" />
                                        <rect x="300.6" y="52.23" width="36.92" height="11.46" rx="5.73" ry="5.73"
                                            fill="#0e80ac" />
                                        <rect x="300.6" y="83.23" width="36.92" height="11.46" rx="5.73" ry="5.73"
                                            fill="#0e80ac" />
                                    </g>
                                </g>

                                <g>
                                    <g>
                                        <rect x="128.92" width="122.45" height="384" rx="21.91" ry="21.91" fill="#fa6931" />
                                        <rect x="128.92" width="95.85" height="384" rx="21.91" ry="21.91" fill="#fd7e42" />
                                    </g>
                                    <g>
                                        <circle cx="190.14" cy="312.79" r="34.57" fill="#fff" />
                                        <circle cx="190.14" cy="312.79" r="15.53" fill="#3c5959" />
                                    </g>
                                    <g>
                                        <rect x="155.57" y="25.94" width="69.13" height="159.59" rx="9.29" ry="9.29"
                                            fill="#fff" />
                                        <rect x="171.68" y="52.23" width="36.92" height="11.46" rx="5.73" ry="5.73"
                                            fill="#0e80ac" />
                                        <rect x="171.68" y="83.23" width="36.92" height="11.46" rx="5.73" ry="5.73"
                                            fill="#0e80ac" />
                                    </g>
                                </g>

                                <g>
                                    <g>
                                        <rect width="122.45" height="384" rx="21.91" ry="21.91" fill="#057195" />
                                        <rect width="95.85" height="384" rx="21.91" ry="21.91" fill="#0e80ac" />
                                    </g>
                                    <g>
                                        <circle cx="61.22" cy="312.79" r="34.57" fill="#fff" />
                                        <circle cx="61.22" cy="312.79" r="15.53" fill="#3c5959" />
                                    </g>
                                    <g>
                                        <rect x="26.66" y="25.94" width="69.13" height="159.59" rx="9.29" ry="9.29"
                                            fill="#fff" />
                                        <rect x="42.76" y="52.23" width="36.92" height="11.46" rx="5.73" ry="5.73"
                                            fill="#0e80ac" />
                                        <rect x="42.76" y="83.23" width="36.92" height="11.46" rx="5.73" ry="5.73"
                                            fill="#0e80ac" />
                                    </g>
                                </g>
                            </svg>

                            <span class="text-lg font-semibold text-slate-700">
                                {{ $year }}
                            </span>

                        </div>
                    </button>

                @endforeach
            </div>

            @foreach($groupedJournals as $year => $medicalJournals)
                <div x-show="selectedYear === {{ $year }}" style="display:none" x-init="$el.style.display = null"
                    class="tr-slide-in">

                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-1.5 h-8 bg-orion-blue rounded-full"></div>

                        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">
                            {{ $year }}
                        </h2>

                        <div class="flex-1 h-px bg-linear-to-r from-orion-blue/40 to-transparent"></div>

                        <button @click="clearYear()" class="text-slate-400 hover:text-rose-500 cursor-pointer transition-colors"
                            title="Close">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>

                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                        <div class="divide-y divide-slate-100">
                            @foreach($medicalJournals as $index => $mj)
                                <div
                                    class="group flex flex-col sm:flex-row items-center gap-6 px-6 py-4 transition-all duration-300">

                                    <div class="flex items-center gap-5 shrink-0">
                                        <span class="text-sm text-slate-300">
                                            {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                        </span>
                                        <div class="text-rose-500 flex items-center justify-center">
                                            <i class="fa-solid fa-file-pdf text-3xl"></i>
                                        </div>
                                    </div>

                                    <div class="grow text-center sm:text-left">
                                        <h3
                                            class="text-base font-semibold text-slate-700 capitalize group-hover:text-orion-blue transition-colors leading-snug">
                                            {{ $mj->title }}
                                        </h3>
                                    </div>

                                    <div class="flex items-center gap-3 shrink-0">
                                        <a href="{{ url($menu->full_slug . '/' . $mj->year . '/' . $mj->filename) }}"
                                            target="_blank"
                                            class="flex items-center gap-2 px-4 py-2 rounded-lg bg-white text-orion-blue hover:bg-orion-blue hover:text-white font-bold text-xs transition-all duration-300 border border-slate-200 hover:border-orion-blue"
                                            title="View Journal">
                                            <i class="fa-solid fa-eye text-[10px]"></i>
                                            View
                                        </a>

                                        <a href="{{ url($menu->full_slug . '/' . $mj->year . '/' . $mj->filename) }}"
                                            download="{{ $mj->title }}.pdf"
                                            class="w-9 h-9 rounded-lg bg-white text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-all duration-300 border border-slate-100"
                                            title="Download PDF">
                                            <i class="fa-solid fa-download text-xs"></i>
                                        </a>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            @endforeach

        </div>
    </div>
@endsection