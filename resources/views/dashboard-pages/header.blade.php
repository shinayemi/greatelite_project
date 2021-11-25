<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="refresh" content="450">
    <meta charset="UTF-8">
    <meta content="ie=edge" http-equiv="x-ua-compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <META NAME="robots" CONTENT="noindex,nofollow"> <!-- do want search engine to index these pages -->
    <title>{{$pageTitle}}</title>

    <!--favicon-->
    <link href="{{ asset('assets/images/favicon.png') }}" rel="shortcut icon">

    <!--Preloader-CSS-->
    <link rel="stylesheet" href="{{ asset('assets/plugins/preloader/preloader.css') }}">

    <!--bootstrap-4-->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    <!--Custom Scroll-->
    <link rel="stylesheet" href="{{ asset('assets/plugins/customScroll/jquery.mCustomScrollbar.min.css') }}">
    <!--Font Icons-->
    <link rel="stylesheet" href="{{ asset('assets/icons/simple-line/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/icons/dripicons/dripicons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/icons/ionicons/css/ionicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/icons/eightyshades/eightyshades.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/icons/fontawesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/icons/foundation/foundation-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/icons/metrize/metrize.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/icons/typicons/typicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/icons/weathericons/css/weather-icons.min.css') }}">

    <!--Date-range-->
    <link rel="stylesheet" href="{{ asset('assets/plugins/date-range/daterangepicker.css') }}">
    <!--Drop-Zone-->
    <link rel="stylesheet" href="{{ asset('assets/plugins/dropzone/dropzone.css') }}">
    <!--Full Calendar-->
    <link rel="stylesheet" href="{{ asset('assets/plugins/full-calendar/fullcalendar.min.css') }}">
    <!--Normalize Css-->
    <link rel="stylesheet" href="{{ asset('assets/css/normalize.css') }}">
    <!--Main Css-->
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">

    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>

</head>

<body>

    <!---Preloader Starts Here--->
    <div id="ip-container" class="ip-container">
        <header class="ip-header">
            <h1 class="ip-logo text-center"><img class="img-fluid" src="{{$appUrl}}assets/images/logo-c.png" alt="" class="ip-logo text-center" /></h1>
            <div class="ip-loader">
                <svg class="ip-inner" width="60px" height="60px" viewBox="0 0 80 80">
                    <path class="ip-loader-circlebg" d="M40,10C57.351,10,71,23.649,71,40.5S57.351,71,40.5,71 S10,57.351,10,40.5S23.649,10,40.5,10z" />
                    <path id="ip-loader-circle" class="ip-loader-circle" d="M40,10C57.351,10,71,23.649,71,40.5S57.351,71,40.5,71 S10,57.351,10,40.5S23.649,10,40.5,10z" />
                </svg>
            </div>
        </header>
    </div>
    <!---Preloader Ends Here--->