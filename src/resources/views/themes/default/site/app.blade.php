<!DOCTYPE HTML>
<html lang="en">
<head>
    {!! config('settings.google_analytics') !!}
    {!! config('settings.gtm_head') !!}
    <link rel="shortcut icon" href="{{ Asset::load('img/favicon.ico') }}"  >
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ config('settings.site_name') }}</title>
    @include('theme::site.partials.styles')
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.7.3/dist/alpine.min.js" defer></script>
</head>
<body class="bg-black white roboto  drawer drawer--right ">
{!! config('settings.gtm_body') !!}
<div id="beanEater" class="flex justify-center items-center min-vh-100 w-100 fixed z-max bg-black">
    <img src="{{ Asset::load('/img/bean_eater.svg') }}" alt="">
</div>



@include('theme::site.partials.header')

<main role="main">
    @yield('content')
</main>

@include('theme::site.partials.footer')
@include('theme::site.partials.scripts')


</body>
</html>
