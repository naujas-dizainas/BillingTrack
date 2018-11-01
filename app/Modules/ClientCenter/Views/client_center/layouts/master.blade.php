<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <link rel="stylesheet" href="/css/app.css">

    <script src="/js/app.js"></script>

    <title>{{ config('fi.headerTitleText') }}</title>

    @include('layouts._head')

    @include('layouts._js_global')

    @yield('head')

    @yield('javascript')

</head>
<body class="{{ $skinClass }} sidebar-mini fixed">

<div class="wrapper">

    <header class="main-header">

        <a href="{{ route('clientCenter.dashboard')}}" class="logo">
            <span class="logo-lg">{{ config('fi.headerTitleText') }}</span>
        </a>

        <nav class="navbar navbar-static-top" role="navigation">
            {{--<a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">--}}
                {{--<span class="sr-only">Toggle navigation</span>--}}
                {{--<span class="icon-bar"></span>--}}
                {{--<span class="icon-bar"></span>--}}
                {{--<span class="icon-bar"></span>--}}
            {{--</a>--}}
            <ul class="nav navbar-nav">
                <li><a href="#" class="nav-link" data-toggle="push-menu" ><i class="fa fa-bars"></i></a></li>
            </ul>

            @yield('header')

        </nav>
    </header>

    <aside class="main-sidebar">

        <section class="sidebar">

            @yield('sidebar')

        </section>

    </aside>

    <div class="content-wrapper">
        @yield('content')
    </div>

</div>

<div id="modal-placeholder"></div>

</body>
</html>