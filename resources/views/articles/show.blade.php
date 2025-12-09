@extends('layouts.app')

@section('title', $article->title)

@section('content')
<article class="article-single">
    <h1>{{ $article->title }}</h1>

    <div class="article-meta">
        <span>Автор: {{ $article->author ?? 'Неизвестен' }}</span>
        <span>{{ $article->created_at->format('d.m.Y H:i') }}</span>
    </div>

    <div class="article-content">
        {!! nl2br(e($article->content)) !!}
    </div>

    <div class="article-actions">
        <a href="{{ route('articles.index') }}" class="btn-secondary">← Назад к списку</a>
        @can('update', $article)
            <a href="{{ route('articles.edit', $article->id) }}" class="btn">Редактировать</a>
        @endcan
        @can('delete', $article)
            <form action="{{ route('articles.destroy', $article->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-delete" onclick="return confirm('Удалить статью?')">Удалить</button>
            </form>
        @endcan
    </div>
</article>
@endsection
