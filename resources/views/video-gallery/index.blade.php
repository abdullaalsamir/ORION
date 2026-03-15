@extends('layouts.app')
@section('title', $menu->name)

@section('content')
    <div class="flex flex-col px-4 md:px-0 mb-12 mt-2">
        <div class="w-full">
            @if(!empty($menu->content))
                <div class="page-content prose max-w-none text-slate-600 mt-2">
                    {!! $menu->content !!}
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
            @foreach($videos as $video)
                <div
                    class="flex flex-col bg-white rounded-2xl overflow-hidden border border-slate-200 hover:border-orion-blue transition-colors duration-200">

                    <div class="w-full aspect-video bg-black relative overflow-hidden">

                        <!-- shimmer -->
                        <div class="absolute inset-0 shimmer video-shimmer"></div>

                        <video class="plyr-video w-full h-full object-cover opacity-0" playsinline preload="none"
                            poster="{{ url('video-gallery-files/thumbnails/' . basename($video->thumbnail_path)) }}">
                            <source src="{{ url('video-gallery-files/videos/' . basename($video->video_path)) }}"
                                type="video/{{ pathinfo($video->video_path, PATHINFO_EXTENSION) }}" />
                        </video>

                    </div>

                    <div class="p-4 flex-1 flex flex-col">
                        <h3 class="text-lg font-semibold text-slate-900">{{ $video->title }}</h3>
                    </div>

                </div>
            @endforeach
        </div>
    </div>
@endsection