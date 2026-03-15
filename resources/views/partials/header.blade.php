<header class="fixed top-0 left-0 w-full z-100">
    <div class="relative z-50 bg-white shadow-sm">
        <div class="container mx-auto w-[90%] max-w-350">
            <div class="flex justify-between items-center h-22.5">
                
                <div class="shrink-0">
                    <a href="{{ url('/') }}" class="flex items-center">
                        <img src="{{ asset('logo.svg') }}" alt="Logo" class="h-18 w-auto">
                    </a>
                </div>

                <nav class="hidden lg:flex h-full">
                    <ul class="flex h-full">
                        @foreach($menus as $menu)
                            @if($menu->isEffectivelyActive())
                                @php 
                                    $hasChildren = $menu->children->count() > 0; 
                                    $href1 = !$hasChildren ? url($menu->full_slug) : 'javascript:void(0)';
                                    $target1 = '_self';
                                    if (!$hasChildren && $menu->concern && $menu->concern->is_redirect && !empty($menu->concern->web_address)) {
                                        $href1 = $menu->concern->web_address;
                                        $target1 = '_blank';
                                    }
                                @endphp

                                <li class="relative group flex items-center h-22.5">
                                    <a href="{{ $href1 }}" target="{{ $target1 }}"
                                    class="relative z-50 px-3.5 h-full text-[15px] font-semibold text-orion-blue group-hover:bg-orion-blue group-hover:text-white flex items-center gap-1 transition-all duration-200">
                                        {{ $menu->name }}
                                        @if($hasChildren)
                                            <i class="fa-solid fa-chevron-down text-[10px] transition-transform group-hover:rotate-180"></i>
                                        @endif
                                    </a>

                                    @if($hasChildren)
                                        <div class="absolute top-full left-0 min-w-64 w-max pointer-events-none group-hover:pointer-events-auto" 
                                            style="clip-path: inset(0px -1000px -1000px -1000px);">
                                            
                                            <ul class="level-2-menu w-full bg-orion-blue shadow-xl py-0 border-t border-white/10 relative">
                                                @foreach($menu->children as $submenu)
                                                    @if($submenu->isEffectivelyActive())
                                                        @php 
                                                            $hasSub = $submenu->children->count() > 0; 
                                                            $href2 = !$hasSub ? url($submenu->full_slug) : 'javascript:void(0)';
                                                            $target2 = '_self';
                                                            if (!$hasSub && $submenu->concern && $submenu->concern->is_redirect && !empty($submenu->concern->web_address)) {
                                                                $href2 = $submenu->concern->web_address;
                                                                $target2 = '_blank';
                                                            }
                                                        @endphp

                                                        <li class="relative group/sub">
                                                            <a href="{{ $href2 }}" target="{{ $target2 }}"
                                                            class="relative z-30 bg-orion-blue flex items-center justify-between px-5 py-3 text-[15px] text-white group-hover/sub:bg-[#1a62ae] transition-colors border-b border-white/10">
                                                                {{ $submenu->name }}
                                                                @if($hasSub)
                                                                    <i class="fa-solid fa-chevron-right text-[10px] sub-chevron"></i>
                                                                @endif
                                                            </a>

                                                            @if($hasSub)
                                                                <ul class="level-3-menu absolute top-0 min-w-64 w-max bg-[#1a62ae] shadow-xl py-0 opacity-0 invisible z-10">
                                                                    @foreach($submenu->children as $subsubmenu)
                                                                        @php
                                                                            $href3 = url($subsubmenu->full_slug);
                                                                            $target3 = '_self';
                                                                            if ($subsubmenu->concern && $subsubmenu->concern->is_redirect && !empty($subsubmenu->concern->web_address)) {
                                                                                $href3 = $subsubmenu->concern->web_address;
                                                                                $target3 = '_blank';
                                                                            }
                                                                        @endphp
                                                                        <li class="border-b border-white/5 last:border-0">
                                                                            <a href="{{ $href3 }}" target="{{ $target3 }}"
                                                                            class="block px-5 py-3 text-[15px] text-white hover:bg-[#2576c7] transition-colors duration-200">
                                                                                {{ $subsubmenu->name }}
                                                                            </a>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </nav>

                <button class="lg:hidden p-2 text-orion-blue" onclick="document.getElementById('mobile-nav').classList.toggle('hidden')">
                    <i class="fa-solid fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
    </div>
    <div id="mobile-nav" class="hidden lg:hidden bg-white border-t border-gray-100 relative z-60">
        <div class="px-4 py-4 space-y-1">
            @foreach($menus as $menu)
                @php
                    $hrefM = url($menu->full_slug);
                    $targetM = '_self';
                    if ($menu->children->count() == 0 && $menu->concern && $menu->concern->is_redirect && !empty($menu->concern->web_address)) {
                        $hrefM = $menu->concern->web_address;
                        $targetM = '_blank';
                    }
                @endphp
                <a href="{{ $hrefM }}" target="{{ $targetM }}" class="block px-3 py-2 text-base font-medium text-orion-blue hover:bg-gray-50">{{ $menu->name }}</a>
            @endforeach
        </div>
    </div>
</header>