<!DOCTYPE html>
<html lang="en">

<head>
    @include('headerscriptgoggle')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('asset/loader.png') }}" type="image/x-icon">
    <title>Onboardify</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Hind:wght@300;400;500;600;700&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

</head>

<body class="d-flex h-100 text-center">
    @include('bodyscriptgoogle')
    <?php
    $settings = Session::get('settings');
    // dd($settings);
    echo " <style>
                                    .btn-gradiant,.btn-custom {
                                          background: {$settings->button_bg} !important;
                                      }
                                    .custom-banner {
                                      background: {$settings->banner_bg} !important;
                                    }
                                    .site-bg{
                                      background: {$settings->site_bg} !important;
                                    }
                                      </style>";
    ?>
    <div class="login-cover-container" style="width:100vw;">
    <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
        <header class="mb-auto pb-3 pt-3">
            <div>
               
            </div>
        </header>
        <div>
