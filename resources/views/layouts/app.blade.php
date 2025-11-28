<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('normalize.css') }}">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
<body>
    <!-- Header -->
    <header>
        <nav>
            <a href="{{ route('home') }}">Главная</a>
            <a href="{{ route('about') }}">О нас</a>
            <a href="{{ route('contacts') }}">Контакты</a>
        </nav>
    </header>

    <!-- Main -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        <p>Воротилин Илья Андреевич 241-321</p>
    </footer>
</body>
</html>
