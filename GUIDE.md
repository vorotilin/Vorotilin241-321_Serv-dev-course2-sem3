# Шпаргалка по выполнению работ

## Домашнее задание 3: Работа с комментариями

### Создание модели, миграции, контроллера и фабрики
**Требование:** Создать новую модель Comment с миграцией, контроллером и фабрикой.

**Теория:** Laravel позволяет создавать все необходимые файлы одной командой используя флаги: `-m` (миграция), `-c` (контроллер), `-f` (фабрика).

**Реализация:**
```bash
php artisan make:model Comment -mcf
```

Это создает:
- `app/Models/Comment.php` - модель
- `database/migrations/xxxx_create_comments_table.php` - миграция
- `app/Http/Controllers/CommentController.php` - контроллер
- `database/factories/CommentFactory.php` - фабрика

### Заполнение миграции
**Требование:** Создать таблицу comments со связью с articles.

**Теория:** Миграции описывают структуру таблиц БД. `foreignId()->constrained()` создает внешний ключ с каскадным удалением.

**Реализация:** В файле миграции `create_comments_table.php`:
```php
$table->id();
$table->foreignId('article_id')->constrained()->onDelete('cascade');
$table->text('content');
$table->timestamps();
```

### Настройка отношений между моделями
**Требование:** Настроить связи Article ↔ Comment.

**Теория:** Eloquent ORM использует методы для определения отношений: `hasMany()` (один ко многим), `belongsTo()` (принадлежит к).

**Реализация:**

В `app/Models/Article.php`:
```php
public function comments()
{
    return $this->hasMany(Comment::class);
}
```

В `app/Models/Comment.php`:
```php
public function article()
{
    return $this->belongsTo(Article::class);
}
```

### Заполнение фабрики
**Требование:** Создать фабрику для генерации тестовых комментариев.

**Теория:** Фабрики используют Faker для генерации случайных данных.

**Реализация:** В `database/factories/CommentFactory.php`:
```php
public function definition()
{
    return [
        'article_id' => \App\Models\Article::factory(),
        'content' => $this->faker->paragraph(),
    ];
}
```

### CRUD методы для комментариев
**Требование:** Реализовать Create, Read, Update, Delete для комментариев.

**Теория:** RESTful подход: store (создание), edit/update (редактирование), destroy (удаление).

**Реализация:** В `app/Http/Controllers/CommentController.php`:
```php
// Создание
public function store(Request $request, $articleId) {
    $validated = $request->validate(['content' => 'required|string|min:3']);
    $article = Article::findOrFail($articleId);
    $article->comments()->create($validated);
    return redirect()->route('articles.show', $articleId);
}

// Редактирование
public function edit($id) {
    $comment = Comment::findOrFail($id);
    return view('comments.edit', compact('comment'));
}

// Обновление
public function update(Request $request, $id) {
    $comment = Comment::findOrFail($id);
    $validated = $request->validate(['content' => 'required|string|min:3']);
    $comment->update($validated);
    return redirect()->route('articles.show', $comment->article_id);
}

// Удаление
public function destroy($id) {
    $comment = Comment::findOrFail($id);
    $articleId = $comment->article_id;
    $comment->delete();
    return redirect()->route('articles.show', $articleId);
}
```

### Каскадное удаление
**Требование:** При удалении статьи удалять все комментарии.

**Теория:** `onDelete('cascade')` в миграции автоматически удаляет связанные записи.

**Реализация:** Уже реализовано в миграции через `->onDelete('cascade')`.

---

## Домашнее задание 4: Авторизация комментариев

### Добавление автора комментария
**Требование:** Добавить поле user_id в таблицу comments.

**Теория:** Для добавления поля в существующую таблицу создается новая миграция с `--table` флагом.

**Реализация:**
```bash
php artisan make:migration add_user_id_to_comments_table --table=comments
```

В миграции:
```php
$table->foreignId('user_id')->nullable()->after('article_id')->constrained()->onDelete('cascade');
```

Обновить модель Comment:
```php
protected $fillable = ['article_id', 'user_id', 'content'];

public function user()
{
    return $this->belongsTo(User::class);
}
```

### Настройка Gates для авторизации
**Требование:** Читатель может добавлять комментарии, автор - редактировать/удалять свои.

**Теория:** Gates - механизм авторизации Laravel. Определяются в `AuthServiceProvider`.

**Реализация:** В `app/Providers/AuthServiceProvider.php`:
```php
Gate::define('update-comment', function ($user, $comment) {
    return $user->id === $comment->user_id;
});

Gate::define('delete-comment', function ($user, $comment) {
    return $user->id === $comment->user_id;
});
```

В контроллере:
```php
$this->authorize('update-comment', $comment);
```

В Blade:
```blade
@can('update-comment', $comment)
    <a href="{{ route('comments.edit', $comment->id) }}">Редактировать</a>
@endcan
```

---

## Работа 8: Email рассылка

### Создание Mail класса
**Требование:** Создать класс для отправки уведомлений о новой статье.

**Теория:** Laravel Mail - система для отправки email с использованием Mailable классов.

**Реализация:**
```bash
php artisan make:mail NewArticleNotification
```

В `app/Mail/NewArticleNotification.php`:
```php
public $article;

public function __construct(Article $article)
{
    $this->article = $article;
}

public function build()
{
    return $this->subject('Новая статья: ' . $this->article->title)
                ->view('emails.new-article');
}
```

### Создание шаблона письма
**Требование:** Сверстать Blade шаблон для email.

**Реализация:** Создать `resources/views/emails/new-article.blade.php`:
```blade
<h1>Добавлена новая статья</h1>
<h2>{{ $article->title }}</h2>
<p><strong>Автор:</strong> {{ $article->author ?? 'Неизвестен' }}</p>
<p>{{ $article->content }}</p>
```

### Отправка email
**Требование:** Отправлять email модератору при создании статьи.

**Теория:** Фасад Mail используется для отправки писем.

**Реализация:** В `ArticleController@store`:
```php
use Illuminate\Support\Facades\Mail;
use App\Mail\NewArticleNotification;

$article = Article::create($validated);
$moderatorEmail = env('MODERATOR_EMAIL', 'moderator@example.com');
Mail::to($moderatorEmail)->send(new NewArticleNotification($article));
```

### Настройка окружения
**Требование:** Настроить SMTP для отправки через mail.ru.

**Реализация:** В `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mail.ru
MAIL_PORT=465
MAIL_USERNAME=your-email@mail.ru
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=your-email@mail.ru
MAIL_FROM_NAME="${APP_NAME}"
```

---

## Работа 9: Модерация комментариев

### Добавление поля модерации
**Требование:** Добавить поле is_approved в таблицу comments.

**Реализация:**
```bash
php artisan make:migration add_is_approved_to_comments_table --table=comments
```

В миграции:
```php
$table->boolean('is_approved')->default(false)->after('content');
```

### Интерфейс модерации
**Требование:** Страница со списком комментариев на модерации (только для модератора).

**Реализация:**

Методы в `CommentController`:
```php
// Список комментариев на модерации
public function moderation()
{
    $comments = Comment::where('is_approved', false)
                       ->with(['article', 'user'])
                       ->orderBy('created_at', 'desc')
                       ->get();
    return view('comments.moderation', compact('comments'));
}

// Одобрение
public function approve($id)
{
    $comment = Comment::findOrFail($id);
    $comment->update(['is_approved' => true]);
    return redirect()->back();
}

// Отклонение
public function reject($id)
{
    $comment = Comment::findOrFail($id);
    $comment->delete();
    return redirect()->back();
}
```

### Фильтрация комментариев
**Требование:** Показывать только одобренные комментарии на странице статьи.

**Реализация:** В `articles/show.blade.php`:
```blade
@php
    $approvedComments = $article->comments->where('is_approved', true);
@endphp

@foreach($approvedComments as $comment)
    <!-- Отображение комментария -->
@endforeach
```

---

## Работа 10: Очереди Laravel

### Создание таблицы очередей
**Требование:** Создать таблицу для хранения заданий.

**Теория:** Laravel Queues позволяет отложить выполнение задач (например, отправку email).

**Реализация:**
```bash
php artisan queue:table
```

Это создает миграцию для таблицы jobs.

### Настройка драйвера очереди
**Требование:** Использовать database драйвер.

**Реализация:** В `.env`:
```env
QUEUE_CONNECTION=database
```

### Создание Job
**Требование:** Создать задание для отправки email.

**Реализация:**
```bash
php artisan make:job SendNewArticleNotification
```

В `app/Jobs/SendNewArticleNotification.php`:
```php
public $article;

public function __construct(Article $article)
{
    $this->article = $article;
}

public function handle()
{
    $moderatorEmail = env('MODERATOR_EMAIL', 'moderator@example.com');
    Mail::to($moderatorEmail)->send(new NewArticleNotification($this->article));
}
```

### Использование Job
**Требование:** Отправлять email через очередь.

**Реализация:** В `ArticleController@store`:
```php
use App\Jobs\SendNewArticleNotification;

$article = Article::create($validated);
SendNewArticleNotification::dispatch($article);
```

### Запуск обработчика очереди
**Требование:** Запустить worker для обработки заданий.

**Реализация:**
```bash
php artisan queue:work
```

Worker будет обрабатывать задания из очереди в фоновом режиме.

---

## Полезные команды

### Миграции
```bash
php artisan migrate                 # Выполнить миграции
php artisan migrate:fresh           # Пересоздать БД
php artisan migrate:fresh --seed    # Пересоздать БД и выполнить сиды
```

### Создание файлов
```bash
php artisan make:model Name -mcf    # Модель + миграция + контроллер + фабрика
php artisan make:migration name     # Миграция
php artisan make:controller Name    # Контроллер
php artisan make:mail Name          # Mail класс
php artisan make:job Name           # Job
```

### Очереди
```bash
php artisan queue:table             # Создать миграцию для очередей
php artisan queue:work              # Запустить worker
php artisan queue:failed-table      # Таблица для failed jobs
```
