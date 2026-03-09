<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        @hasSection('title')
            @yield('title') | ORION
        @else
            ORION
        @endif
    </title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preload" as="image" href="{{ asset('logo.svg') }}">

    <meta name="description" content="ORION">

    <meta property="og:title" content="@yield('meta_title', 'ORION')">
    <meta property="og:description" content="@yield('meta_description', 'ORION')">
    <meta property="og:image" content="@yield('meta_image', asset('logo.svg'))">
    <meta property="og:url" content="{{ request()->fullUrl() }}">
    <meta property="og:type" content="website">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('meta_title', 'ORION')">
    <meta name="twitter:description" content="@yield('meta_description', 'ORION')">
    <meta name="twitter:image" content="@yield('meta_image', asset('logo.svg'))">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex flex-col min-h-screen overflow-x-hidden bg-slate-50">

    @include('partials.header')

    @if(request()->is('/'))
        @include('partials.swiper-slider')
    @endif

    <main class="grow w-full {{ request()->is('/') ? 'pt-0' : 'pt-22.5' }} pb-16">
        <div class="container mx-auto w-[90%] max-w-350">
            @isset($menu)
                <div class="flex flex-col">
                    <div class="w-full">
                        <h1 class="w-full">
                            <span class="px-4 {{ request()->is('/') ? 'text-4xl' : '' }}">
                                {{ request()->is('/') ? 'Welcome to Orion Pharma' : $menu->name }}
                            </span>
                        </h1>
                    </div>

                    @if(!empty($menu->content))
                        <div class="page-content prose max-w-none text-slate-700 text-justify leading-relaxed">
                            {!! $menu->content !!}
                        </div>
                    @endif

                    @yield('content')
                </div>
            @else
                @yield('content')
            @endisset
        </div>
    </main>

    @if(request()->is('/'))
        <div class="container mx-auto w-[90%] max-w-350 pb-16">
            @include('partials.home-csr')
            @include('partials.home-products')
            @include('partials.home-news-and-announcements')
        </div>
    @endif

    @include('partials.footer')

</body>

</html>