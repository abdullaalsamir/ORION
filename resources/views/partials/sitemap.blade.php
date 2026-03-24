@extends('layouts.app')

@section('title', 'Sitemap')

@section('content')
    <div class="relative rounded-2xl border border-slate-200 bg-white p-8">

        @foreach($menus as $item)
            <div class="{{ !$loop->last ? 'mb-8' : '' }}">
                <div class="flex items-center gap-1 mb-2">
                    <div class="w-10 h-10 flex items-center justify-center text-slate-400">
                        <i class="fas fa-bars"></i>
                    </div>

                    @if($item->children->count() > 0)
                        <div class="text-lg text-orion-blue font-semibold">
                            {{ $item->name }}
                        </div>
                    @else
                        <a href="{{ url('/' . $item->full_slug) }}"
                            class="inline-block text-lg text-orion-blue font-semibold hover:text-orion-blue transition-transform duration-200 hover:translate-x-1">
                            {{ $item->name }}
                        </a>
                    @endif
                </div>

                @if($item->children->count() > 0)

                    <ul class="relative">

                        @foreach($item->children as $child)
                            <li class="relative {{ !$loop->last ? 'pb-3' : '' }}">

                                @if(!$loop->last)
                                    <div class="absolute left-5 top-0 bottom-0 w-px bg-slate-400"></div>
                                @endif

                                <div class="relative">

                                    @if($loop->last)
                                        <div class="absolute left-5 top-0 bottom-1/2 w-px bg-slate-400"></div>
                                    @endif

                                    <div class="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-px bg-slate-400"></div>

                                    @if($child->children->count() > 0)
                                        <span class="block pl-11 py-1 text-base font-semibold text-orion-blue">
                                            {{ $child->name }}
                                        </span>
                                    @else
                                        <a href="{{ url('/' . $child->full_slug) }}"
                                            class="inline-block pl-11 py-1 text-base font-semibold text-slate-500 hover:text-orion-blue transition-transform duration-200 hover:translate-x-1">
                                            {{ $child->name }}
                                        </a>
                                    @endif
                                </div>

                                @if($child->children->count() > 0)

                                    <ul class="relative mt-2">

                                        @foreach($child->children as $subchild)
                                            <li class="relative {{ !$loop->last ? 'pb-3' : '' }}">

                                                @if(!$loop->last)
                                                    <div class="absolute left-11 top-0 bottom-0 w-px bg-slate-400"></div>
                                                @endif

                                                <div class="relative">

                                                    @if($loop->last)
                                                        <div class="absolute left-11 top-0 bottom-1/2 w-px bg-slate-400"></div>
                                                    @endif

                                                    <div class="absolute left-11 top-1/2 -translate-y-1/2 w-4 h-px bg-slate-400"></div>

                                                    <a href="{{ url('/' . $subchild->full_slug) }}"
                                                        class="inline-block pl-17 py-1 text-base font-semibold text-slate-400 hover:text-orion-blue transition-transform duration-200 hover:translate-x-1">
                                                        {{ $subchild->name }}
                                                    </a>
                                                </div>
                                            </li>
                                        @endforeach

                                    </ul>

                                @endif
                            </li>
                        @endforeach

                    </ul>

                @endif
            </div>
        @endforeach

    </div>
@endsection