<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;

// Работа 1 + Работа 2: Главная страница с JSON
Route::get('/', [MainController::class, 'index'])->name('home');

// Работа 1: О нас
Route::get('/about', function () {
    return view('about');
})->name('about');

// Работа 1: Контакты
Route::get('/contacts', function () {
    $contacts = [
        [
            'person' => 'Воротилин Илья Андреевич',
            'phone' => '241-321',
            'email' => 'vorotilin@example.com'
        ],
        [
            'person' => 'Петров Петр Петрович',
            'phone' => '242-322',
            'email' => 'petrov@example.com'
        ],
        [
            'person' => 'Сидоров Сидор Сидорович',
            'phone' => '243-323',
            'email' => 'sidorov@example.com'
        ]
    ];

    return view('contacts', ['contacts' => $contacts]);
})->name('contacts');

// Работа 2: Галерея
Route::get('/galery/{id}', [MainController::class, 'galery'])->name('galery');

Route::get('/signin', [AuthController::class, 'create'])->name('signin');
Route::post('/signin', [AuthController::class, 'registration'])->name('registration');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('articles', ArticleController::class)->except(['index', 'show']);
});

Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');
