<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Article;

class CommentController extends Controller
{
    // Создание комментария
    public function store(Request $request, $articleId)
    {
        $validated = $request->validate([
            'content' => 'required|string|min:3',
        ]);

        $article = Article::findOrFail($articleId);

        $validated['user_id'] = auth()->id();
        $article->comments()->create($validated);

        return redirect()->route('articles.show', $articleId)->with('success', 'Комментарий отправлен на модерацию');
    }

    // Редактирование комментария
    public function edit($id)
    {
        $comment = Comment::findOrFail($id);
        $this->authorize('update-comment', $comment);
        return view('comments.edit', compact('comment'));
    }

    // Обновление комментария
    public function update(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);
        $this->authorize('update-comment', $comment);

        $validated = $request->validate([
            'content' => 'required|string|min:3',
        ]);

        $comment->update($validated);

        return redirect()->route('articles.show', $comment->article_id)->with('success', 'Комментарий обновлен');
    }

    // Удаление комментария
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $this->authorize('delete-comment', $comment);
        $articleId = $comment->article_id;
        $comment->delete();

        return redirect()->route('articles.show', $articleId)->with('success', 'Комментарий удален');
    }

    // Список комментариев для модерации
    public function moderation()
    {
        $comments = Comment::where('is_approved', false)
                           ->with(['article', 'user'])
                           ->orderBy('created_at', 'desc')
                           ->get();
        return view('comments.moderation', compact('comments'));
    }

    // Одобрение комментария
    public function approve($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->update(['is_approved' => true]);
        return redirect()->back()->with('success', 'Комментарий одобрен');
    }

    // Отклонение комментария
    public function reject($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();
        return redirect()->back()->with('success', 'Комментарий отклонен');
    }
}
// Воротилин Илья 241-321