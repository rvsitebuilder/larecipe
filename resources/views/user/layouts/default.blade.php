<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <title>
            @if(optional($embed)->siteSeoprepend)
              {!! optional($embed)->siteSeoprepend !!}
            @endif
            {{ $title ?? 'Documentation' }}
            @if(optional($embed)->siteSeoappend)
              {!! optional($embed)->siteSeoappend !!}
            @endif
          </title>

        <!-- Favicon -->
        {{-- <link rel="apple-touch-icon" href="{{ asset(config('rvsitebuilder/larecipe.ui.fav')) }}">  --}}
        <link rel="shortcut icon" href="/storage/images/favicon.ico" sizes="32x32"/>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- DNS-Prefetch and Preconnect -->
        <link rel="dns-prefetch" href="{{ config('rvsitebuilder/wysiwyg.wex.cdn.cdnURL') }}">
        <link rel="dns-prefetch" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <!-- SEO -->
        {!! optional($seo)->tag !!}
        {!! $embed->siteMeta !!}
        {!! $embed->pageMeta !!}
        @if($openGraph = config('rvsitebuilder/larecipe.seo.og'))
            @foreach($openGraph as $key => $value)
                @if($value)
                    <meta property="og:{{ $key }}" content="{{ $value }}" />
                @endif
            @endforeach
        @endif
        <meta name="twitter:card" value="summary">

        <!-- Canonical -->
        @if (isset($canonical) && $canonical)
            <link rel="canonical" href="{{ url($canonical) }}" />
        @endif

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Meta from blade section -->
        @yield('meta')

        <!-- CSS -->
        <link rel="stylesheet" href="{{ larecipe_assets('css/app.css') }}">
        <link rel="stylesheet" href="{{ larecipe_assets('css/font-awesome.css') }}">
        @if (config('rvsitebuilder/larecipe.ui.fa_v4_shims', true))
            <link rel="stylesheet" href="{{ larecipe_assets('css/font-awesome-v4-shims.css') }}">
        @endif

        <!-- Dynamic Colors -->
        <style>
            :root {
                --primary: {{ config('rvsitebuilder/larecipe.ui.colors.primary') }};
                --secondary: {{ config('rvsitebuilder/larecipe.ui.colors.secondary') }};
            }
        </style>
        {{ style(mix('css/user/app.css', 'vendor/rvsitebuilder/larecipe')) }}

        {!! $embed->siteCss !!}

        <script>
                window.config = @json([]);

                if(localStorage.getItem('larecipeSidebar') == null) {
                    localStorage.setItem('larecipeSidebar', !! {{ config('larecipe.ui.show_side_bar') ?: 1 }});
                    localStorage.setItem('larecipeSidebar', !! 1);
                }

                // To dynamic changing drop-down menu link on unpoly.js
                const larecipeRoute = secure_url(config('rvsitebuilder/larecipe.docs.route'));
                var currentVersion = "{{ $currentVersion }}";
                var currentLang = "{{ $currentLang }}";

                var larecipeVersion = {};
                @foreach (config('rvsitebuilder/larecipe.versions.published') as $version)
                    larecipeVersion['{{ $version }}'] = `
                    {{  ucfirst($version) }} <i class="mx-1 fa fa-angle-down"></i>
                    `;
                @endforeach

                var larecipeLang = {};
                @foreach(config('rvsitebuilder/larecipe.languages.published') as $lang)
                    larecipeLang['{{ $lang }}'] = `
                    @if(Lang::has('menus.language-picker.img.'.$lang))
                        @lang('menus.language-picker.img.'.$lang)
                        &nbsp; <small> @lang('menus.language-picker.langs.'.$lang) </small>
                        <i class="mx-1 fa fa-angle-down"></i>
                    @else
                        @lang('menus.language-picker.img.default')
                        &nbsp; <small> {{ $lang }} </small>
                        <i class="mx-1 fa fa-angle-down"></i>
                    @endif
                    `;
                @endforeach

                window.Prism = window.Prism || {};
        </script>

        <script src="{{ larecipe_assets('js/app.js') }}"></script>
    </head>
    <body>
        <div id="app" v-cloak>
            @include('rvsitebuilder/larecipe::user.partials.nav')

            @yield('content')

            <larecipe-back-to-top></larecipe-back-to-top>

            @include('rvsitebuilder/larecipe::user.plugins.search')
        </div>

        <div id="displayNone"></div>

        {{ script(mix('js/user/app.js', 'vendor/rvsitebuilder/larecipe')) }}

        @include('rvsitebuilder/wysiwyg::user.inject.viewmode')
    </body>
</html>
