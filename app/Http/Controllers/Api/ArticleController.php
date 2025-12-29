<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    // GET /articles (public)
    public function index(Request $request)
    {
        $query = Article::query()->whereNotNull('published_at')->with('author:id,name');

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($w) use ($q) {
                $w->where('title_en', 'like', '%' . $q . '%')
                  ->orWhere('title_ar', 'like', '%' . $q . '%');
            });
        }

        return response()->json($query->orderByDesc('published_at')->paginate(20));
    }

    // GET /articles/{id} (public)
    public function show(Request $request, int $id)
    {
        $article = Article::with('author:id,name')->findOrFail($id);
        return response()->json(['article' => $article]);
    }

    // POST /articles (trainer)
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'title_en' => ['required', 'string', 'max:255'],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'content_en' => ['required', 'string'],
            'content_ar' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:8192'],
            'published_at' => ['nullable', 'date'],
        ]);

        $imagePath = $request->file('image')?->store('articles', 'public');

        $article = Article::create([
            'title_en' => $validated['title_en'],
            'title_ar' => $validated['title_ar'] ?? null,
            'content_en' => $validated['content_en'],
            'content_ar' => $validated['content_ar'] ?? null,
            'category' => $validated['category'] ?? null,
            'image' => $imagePath,
            'author_id' => $user->id,
            'published_at' => $validated['published_at'] ?? now(),
        ]);

        return response()->json(['article' => $article], 201);
    }
}
