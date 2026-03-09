@if(isset($newsMenu) && ($pinnedNews || $homeNews->isNotEmpty()))
    <section class="mt-16">

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-left text-orion-blue text-2xl uppercase font-bold">
                News & Announcements
            </h2>

            <a href="{{ url($newsMenu->full_slug) }}"
                class="text-slate-400 font-semibold uppercase text-sm tracking-wider hover:text-orion-blue mt-1">
                View All
            </a>
        </div>

        <div class="grid grid-cols-12 gap-8">
                
            <div class="col-span-12 lg:col-span-4">
                <div class="sticky top-24">
                    @if($pinnedNews)
                        <div class="index-card group bg-white rounded-xl overflow-hidden flex flex-col border border-slate-200 hover:border-slate-200!">
                            <div class="aspect-video overflow-hidden shimmer relative border-b border-slate-200">
                                @if($pinnedNews->file_type === 'image')
                                    <img src="{{ url($newsMenu->full_slug . '/' . basename($pinnedNews->file_path)) }}"
                                        class="product-image w-full h-full object-cover transition-transform duration-300">
                                @else
                                    <div class="w-full h-full bg-red-50 flex items-center justify-center">
                                        <i class="fas fa-file-pdf text-5xl text-red-500"></i>
                                    </div>
                                @endif
                                <div class="absolute top-4 left-4">
                                    <span class="bg-orion-blue text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">
                                        Pinned
                                    </span>
                                </div>
                            </div>

                            <div class="p-6 flex flex-col grow">
                                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-3">
                                    {{ $pinnedNews->news_date->format('d F, Y') }}
                                </span>
                                <h3 class="text-xl font-semibold text-slate-900 capitalize group-hover:text-orion-blue transition-all duraion-300 line-clamp-3 mb-3">
                                    {{ $pinnedNews->title }}
                                </h3>
                                <p class="text-slate-500 text-base leading-relaxed line-clamp-3">
                                    {{ $pinnedNews->description }}
                                </p>
                                    
                                <div class="mt-4">
                                    @if($pinnedNews->file_type === 'image')
                                        <a href="{{ url($newsMenu->full_slug . '/' . $pinnedNews->slug) }}"
                                            class="text-orion-blue font-bold text-sm flex items-center gap-2 group/btn">
                                            Read Full News
                                            <i class="fas fa-arrow-right mt-1 group-hover/btn:translate-x-1 transition-transform"></i>
                                        </a>
                                    @else
                                        <a href="{{ url($newsMenu->full_slug . '/' . basename($pinnedNews->file_path)) }}" target="_blank"
                                            class="flex items-center justify-center gap-2 w-full py-2.5 text-slate-700 text-xs font-bold uppercase rounded-lg border border-slate-200 hover:text-orion-blue hover:border-orion-blue transition-all">
                                            View PDF Document
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-white rounded-xl p-10 border-2 border-dashed border-slate-200 text-center text-slate-400">
                            <p class="text-sm font-medium">No Pinned Announcements</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-span-12 lg:col-span-8">
                <div class="space-y-3">
                    @foreach($homeNews as $item)
                        @php 
                            $filename = basename($item->file_path);
                            $fileUrl = url($newsMenu->full_slug . '/' . $filename);
                        @endphp

                        @if($item->file_type === 'image')
                            <a href="{{ url($newsMenu->full_slug . '/' . $item->slug) }}"
                                class="group bg-white border border-slate-200 rounded-xl p-3 flex items-center hover:border-orion-blue transition-all">
                                    
                                <div class="w-50 aspect-video rounded-lg overflow-hidden bg-slate-100 shrink-0 border border-slate-200">
                                    <img src="{{ $fileUrl }}" class="w-full h-full object-cover">
                                </div>

                                <div class="flex-1 min-w-0 ml-5 self-start">
                                    <h4 class="text-lg font-semibold text-slate-900 capitalize group-hover:text-orion-blue transition-all duraion-300 line-clamp-2 -mt-0.5">
                                        {{ $item->title }}
                                    </h4>
                                    <p class="text-slate-500 text-base leading-relaxed line-clamp-1 mt-2">
                                        {{ html_entity_decode(strip_tags($item->description)) }}
                                    </p>
                                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mt-2">
                                        {{ $item->news_date->format('d F, Y') }}
                                    </div>
                                </div>

                                <div class="shrink-0 px-4 text-slate-300 group-hover:text-orion-blue transition-all">
                                    <i class="fas fa-chevron-right text-xs"></i>
                                </div>
                            </a>
                        @else
                            <div class="bg-white border border-slate-200 rounded-xl p-3 flex items-center">
                                <div class="w-50 aspect-video rounded-lg overflow-hidden bg-red-50 shrink-0 flex items-center justify-center border border-red-100">
                                    <i class="fas fa-file-pdf text-3xl text-red-500"></i>
                                </div>

                                <div class="flex-1 min-w-0 ml-5 self-start">
                                    <h4 class="text-lg font-semibold text-slate-900 capitalize group-hover:text-orion-blue transition-all duraion-300 line-clamp-2 -mt-0.5">
                                        {{ $item->title }}
                                    </h4>
                                    <p class="text-slate-500 text-base leading-relaxed line-clamp-1 mt-2">
                                        {{ html_entity_decode(strip_tags($item->description)) }}
                                    </p>
                                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mt-2">
                                        {{ $item->news_date->format('d F, Y') }}
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 shrink-0 px-4">
                                    <a href="{{ $fileUrl }}" target="_blank"
                                        class="flex items-center gap-2 px-4 py-2 rounded-lg bg-white text-orion-blue hover:bg-orion-blue hover:text-white font-bold text-xs transition-all duration-300 border border-slate-200 hover:border-orion-blue"
                                        title="View Announcement">
                                        <i class="fa-solid fa-eye text-[10px]"></i>
                                        View
                                    </a>

                                    <a href="{{ $fileUrl }}" download="{{ $item->title }}.pdf"
                                        class="w-9 h-9 rounded-lg bg-white text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-all duration-300 border border-slate-200"
                                        title="Download PDF">
                                        <i class="fa-solid fa-download text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif