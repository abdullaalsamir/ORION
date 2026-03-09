@if ($paginator->hasPages())
    <div class="w-full flex justify-center mt-16">
        <div class="grid grid-cols-4 grid-rows-2 items-center gap-3 sm:flex sm:justify-center">

            <div class="order-2 col-span-2 flex justify-end gap-2 sm:order-1">

                @if ($paginator->onFirstPage())
                    <span
                        class="rounded-lg px-2 py-1.5 text-sm bg-gray-100 text-slate-400 cursor-not-allowed border border-slate-200 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="rotate-180" height="16" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}"
                        class="rounded-lg px-2 py-1.5 text-sm bg-gray-50 hover:bg-white border border-slate-200 hover:border-orion-blue transition flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="rotate-180" height="16" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                @endif

            </div>

            <div class="order-1 col-span-4 flex justify-center gap-2 sm:order-2 flex-wrap">

                @foreach ($elements as $element)
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="rounded-lg px-3 py-1 text-sm bg-black text-white border border-slate-200">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}"
                                    class="rounded-lg px-3 py-1 text-sm bg-gray-50 hover:bg-white border border-slate-200 hover:border-orion-blue transition">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

            </div>

            <div class="order-2 col-span-2 flex items-center gap-2 sm:order-3">

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}"
                        class="rounded-lg px-2 py-1.5 text-sm bg-gray-50 hover:bg-white border border-slate-200 hover:border-orion-blue transition flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" height="16" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                @else
                    <span
                        class="rounded-lg px-2 py-1.5 text-sm bg-gray-100 text-slate-400 cursor-not-allowed border border-slate-200 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" height="16" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </span>
                @endif

            </div>

        </div>
    </div>
@endif