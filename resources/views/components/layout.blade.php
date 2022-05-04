<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">

    <title>{{ $title }} | {{ config('app.name', 'LightOps') }}</title>
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        html {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji;
            line-height: 1.5
        }
    </style>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"
        referrerpolicy="no-referrer">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"
        referrerpolicy="no-referrer">
    </script>
    <!-- axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- moment -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.3/moment-with-locales.min.js"
        integrity="sha512-vFABRuf5oGUaztndx4KoAEUVQnOvAIFs59y4tO0DILGWhQiFnFHiR+ZJfxLDyJlXgeut9Z07Svuvm+1Jv89w5g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.34/moment-timezone-with-data.js"
        integrity="sha512-oqSECbLRRAy3Sq2tJ0RmzbqXHprFS+n7WapvpI1t0V7CtV4vghscIQ8MYoQo6tp4MrJmih4SlOaYuCkPRi3j6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

</head>

<body>
    <div class="container-sm">
        <header class="d-flex flex-wrap justify-content-between py-3 mb-4 border-bottom">
            <div class="fs-4 text-dark">{{ $title }}</div>
        </header>
        <main class="content">{{ $slot }}</main>
    </div>

    <footer class="fixed-bottom border-top bg-light">
        <div class="container-sm d-flex flex-wrap justify-content-between ">
            <a href="/" class="text-dark text-decoration-none">
                <span>{{ config('app.name', 'LightOps') }}</span>
            </a>
            <div>
                <a href="https://github.com/raikiri-jp" target="_blank" class="text-dark text-decoration-none">
                    https://github.com/raikiri-jp
                </a>
            </div>
        </div>
    </footer>
</body>

</html>
