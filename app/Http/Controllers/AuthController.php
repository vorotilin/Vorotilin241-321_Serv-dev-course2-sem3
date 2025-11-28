<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    // Отображение формы регистрации
    public function create()
    {
        return view('auth.signin');
    }

    // Обработка данных формы с валидацией
    public function registration(Request $request)
    {
        // Валидация входных данных
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
        ], [
            'name.required' => 'Поле "Имя" обязательно для заполнения',
            'email.required' => 'Поле "Email" обязательно для заполнения',
            'email.email' => 'Введите корректный email адрес',
            'password.required' => 'Поле "Пароль" обязательно для заполнения',
            'password.min' => 'Пароль должен содержать минимум 6 символов',
        ]);

        // Возврат данных в формате JSON
        return response()->json([
            'success' => true,
            'message' => 'Регистрация прошла успешно',
            'data' => $validated
        ]);
    }
}
