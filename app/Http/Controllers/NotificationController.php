<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);

        $articleId = $notification->data['article_id'] ?? null;

        $notification->markAsRead();

        if ($articleId) {
            return redirect()->route('articles.show', $articleId);
        }

        return redirect()->route('articles.index');
    }
}
// Воротилин Илья 241-321