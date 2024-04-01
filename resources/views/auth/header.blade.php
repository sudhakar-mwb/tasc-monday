<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your App Title</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body class=" container d-flex h-100 text-center">


    <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
        <header class="mb-auto pb-3 pt-3">
            <div>
                <a href="/monday/form" class="text-decoration-none">
                    <span class="header-logo float-md-start">
                        <img width="140" height="auto" alt="logo" class="logo-image_HXE image-logo_cI-"
                            loading="lazy"
                            data-src="//res2.weblium.site/res/65d24af7b936dd000f78229f/65d250a047a0c800108806d0"
                            data-component="logo"
                            src="//res2.weblium.site/res/65d24af7b936dd000f78229f/65d250a047a0c800108806d0?nowebp"
                            data-was-processed="true">
                    </span>
                </a>
                <nav class="nav nav-masthead justify-content-center align-items-center float-md-end">
                    <span class="text-secondary

            ">
                        Welcome
                    </span>

                    @php
                        $segments = Request::segments();
                    @endphp

                    @if (in_array('form', $segments) && count($segments) > array_search('form', $segments) + 1)
                        <a href="/monday/form" class="text-decoration-none">
                            <button class="btn btn-to-link btn-secondary ms-3 btn-gradiant  d-flex align-items-center"
                                type="button">
                                <span>
                                    Command Center
                                </span>

                                <span class="icon-btn_track" style="height: 22px;width: 22px">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        version="1.1" x="0px" y="0px" viewBox="0 0 100 100"
                                        enable-background="new 0 0 100 100" xml:space="preserve" style="max-width:100%"
                                        height="100%">
                                        <g style="" fill="currentColor">
                                            <g style="" fill="currentColor">
                                                <path fill="currentColor"
                                                    d="M92.013,47.005L77.3,32.29V18.95c0-3.03-2.466-5.496-5.499-5.496h-5.636c-2.081,0-3.897,1.164-4.828,2.876    L50.791,5.784c-1.046-1.046-2.741-1.046-3.787,0L5.783,47.005c-0.766,0.766-0.994,1.919-0.578,2.918    c0.413,1.001,1.39,1.653,2.472,1.653h10.722V82.42c0,6.88,5.598,12.48,12.479,12.48h36.757c6.881,0,12.484-5.601,12.484-12.48    V51.576h10.001c1.084,0,2.059-0.652,2.477-1.653C93.009,48.924,92.777,47.771,92.013,47.005z M67.634,89.545H30.877    c-3.93,0-7.123-3.195-7.123-7.125V50.297c0.265-0.457,0.59-0.875,0.98-1.25h49.043c0.391,0.375,0.715,0.793,0.985,1.25V82.42    C74.763,86.35,71.564,89.545,67.634,89.545z M77.438,46.221c-0.011,0-0.027,0.009-0.039,0.009h-56.28    c-0.017,0-0.027-0.009-0.044-0.009h-6.933l34.756-34.757L61.453,24.02c0.771,0.768,1.927,0.996,2.917,0.581    c1.002-0.416,1.657-1.393,1.657-2.475l0.138-3.316l5.779,0.141v14.448c0,0.71,0.28,1.39,0.781,1.894l10.932,10.929H77.438z"
                                                    style=""></path>
                                            </g>
                                            <g style="" fill="currentColor">
                                                <path fill="currentColor"
                                                    d="M55.255,55.727H43.262c-2.835,0-5.136,2.304-5.136,5.136v11.993c0,2.832,2.301,5.136,5.136,5.136h11.993    c2.829,0,5.136-2.304,5.136-5.136V60.862C60.391,58.03,58.084,55.727,55.255,55.727z M56.114,72.855    c0,0.477-0.386,0.861-0.859,0.861H43.262c-0.479,0-0.864-0.385-0.864-0.861V60.862c0-0.477,0.385-0.861,0.864-0.861h11.993    c0.474,0,0.859,0.385,0.859,0.861V72.855z"
                                                    style=""></path>
                                            </g>
                                        </g>
                                    </svg></span>
                            </button>
                        </a>
                    @endif

                </nav>
            </div>
        </header>
