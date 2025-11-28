@extends('layouts.app')

@section('title', 'Контакты')

@section('content')
<section>
    <h1>Контакты</h1>
    <p><strong>ФИО:</strong> {{ $person }}</p>
    <p><strong>Телефон:</strong> {{ $phone }}</p>
    <p><strong>Email:</strong> {{ $email }}</p>
</section>
@endsection
