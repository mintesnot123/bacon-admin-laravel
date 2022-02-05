<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Demo APP') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
    <meta name="description" content="This is an example dashboard created using build-in elements and components.">
    <meta name="msapplication-tap-highlight" content="no">
    <!-- Scripts -->
    <!--  <script src="{{ asset('js/app.js') }}" defer></script> -->
    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css"> 
    <!-- Styles -->
    <!-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> -->
    <link href="{{ asset('main.css') }}" rel="stylesheet">
</head>
<body>
   
    <div class="app-main">

        <div class="app-main__outer">
            <div class="app-main__inner">
                <div class="row">
                    <div class="col-md-12 col-xl-12">
                        <main class="py-4">
                            <div class="container">
                                @yield('content')
                            </div>

                        </main>
                    </div>
                </div>
            </div>    
            @include('partials.footer')    
        </div>
    </div>
    <script src="https://maps.google.com/maps/api/js?sensor=true"></script>
    <script src="{{ asset('assets/scripts/main.js') }}" defer></script>
</body>
</html>