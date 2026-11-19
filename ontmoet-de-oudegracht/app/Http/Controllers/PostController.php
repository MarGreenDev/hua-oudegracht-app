<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'place' => ['required', 'string', 'max:255'],
            'text_content' => ['required', 'string'],
            'date' => ['required', 'date'],
            'image' => ['nullable', 'image', 'max:5120'],
            'image2' => ['nullable', 'image', 'max:5120'],
            'image3' => ['nullable', 'image', 'max:5120'],
            'image4' => ['nullable', 'image', 'max:5120'],
            'image5' => ['nullable', 'image', 'max:5120'],
        ]);

        foreach (['image', 'image2', 'image3', 'image4', 'image5'] as $field) {
            if ($request->hasFile($field)) {
                $validated[$field] = $request->file($field)->store('posts', 'public');
            }
        }

        Post::create($validated);

        return redirect('/cms')->with('success', 'Post created successfully.');
    }
}
