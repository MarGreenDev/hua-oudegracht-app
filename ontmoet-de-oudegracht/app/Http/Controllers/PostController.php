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
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'video_timestamp' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image'],
            'image2' => ['nullable', 'image'],
            'image3' => ['nullable', 'image'],
            'image4' => ['nullable', 'image'],
            'image5' => ['nullable', 'image'],
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
