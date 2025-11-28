<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\AuthController;

// Работа 1 + Работа 2: Главная страница с JSON
Route::get('/', [MainController::class, 'index'])->name('home');

// Работа 1: О нас
Route::get('/about', function () {
    return view('about');
})->name('about');

// Работа 1: Контакты
Route::get('/contacts', function () {
    return view('contacts', [
        'person' => 'Воротилин Илья Андреевич',
        'phone' => '241-321',
        'email' => 'vorotilin@example.com'
    ]);
})->name('contacts');

// Работа 2: Галерея
Route::get('/galery/{id}', [MainController::class, 'galery'])->name('galery');

// Работа 3: Авторизация
Route::get('/signin', [AuthController::class, 'create'])->name('signin');
Route::post('/signin', [AuthController::class, 'registration'])->name('registration');
