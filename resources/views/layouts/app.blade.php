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
    <header>
        <nav>
            <a href="{{ route('home') }}">Главная</a>
            <a href="{{ route('about') }}">О нас</a>
            <a href="{{ route('contacts') }}">Контакты</a>
            <a href="{{ route('articles.index') }}">Новости</a>
            @auth
                @can('create', \App\Models\Article::class)
                    <a href="{{ route('articles.create') }}">Создать новость</a>
                @endcan
            @endauth
        </nav>

        <div class="auth-section">
            @auth
                <span class="user-name">{{ Auth::user()->name }}</span>
                <a href="{{ route('logout') }}" class="btn-secondary">Выход</a>
            @else
                <a href="{{ route('login.form') }}" class="btn">Вход</a>
                <a href="{{ route('signin') }}" class="btn-secondary">Регистрация</a>
            @endauth
        </div>
    </header>

    <!-- Main -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        <p>Воротилин Илья Андреевич 241-321</p>
    </footer>

    <script src="{{ asset('auth.js') }}"></script>
</body>
</html>
