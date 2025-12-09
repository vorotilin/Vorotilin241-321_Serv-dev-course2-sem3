<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::orderBy('created_at', 'desc')->paginate(10);
        return view('articles.index', compact('articles'));
    }

    public function create()
    {
        $this->authorize('create', Article::class);
        return view('articles.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Article::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'author' => 'nullable|string|max:255',
        ], [
            'title.required' => 'Заголовок обязателен',
            'content.required' => 'Содержание обязательно',
            'content.min' => 'Содержание должно быть минимум 10 символов',
        ]);

        Article::create($validated);

        return redirect()->route('articles.index')->with('success', 'Статья создана');
    }

    public function show($id)
    {
        $article = Article::findOrFail($id);
        return view('articles.show', compact('article'));
    }

    public function edit($id)
    {
        $article = Article::findOrFail($id);
        $this->authorize('update', $article);
        return view('articles.edit', compact('article'));
    }

    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        $this->authorize('update', $article);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'author' => 'nullable|string|max:255',
        ], [
            'title.required' => 'Заголовок обязателен',
            'content.required' => 'Содержание обязательно',
            'content.min' => 'Содержание должно быть минимум 10 символов',
        ]);

        $article->update($validated);

        return redirect()->route('articles.index')->with('success', 'Статья обновлена');
    }

    public function destroy($id)
    {
        $article = Article::findOrFail($id);
        $this->authorize('delete', $article);
        $article->delete();

        return redirect()->route('articles.index')->with('success', 'Статья удалена');
    }
}
