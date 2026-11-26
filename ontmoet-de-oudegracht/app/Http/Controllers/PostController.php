<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Support\VideoRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        return view('CMS.cms', [
            'posts' => Post::orderByDesc('id')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatedPost($request);

        foreach (['image', 'image2', 'image3', 'image4', 'image5'] as $field) {
            if ($request->hasFile($field)) {
                $validated[$field] = $request->file($field)->store('posts', 'public');
            }
        }

        Post::create($validated);

        return redirect()->route('cms.index')->with('success', 'Post created successfully.');
    }

    public function update(Request $request, Post $post)
    {
        $validated = $this->validatedPost($request);

        foreach (['image', 'image2', 'image3', 'image4', 'image5'] as $field) {
            if ($request->hasFile($field)) {
                $newImage = $request->file($field)->store('posts', 'public');
                if ($post->$field) {
                    Storage::disk('public')->delete($post->$field);
                }
                $validated[$field] = $newImage;
            }
        }

        $post->update($validated);

        return redirect()->route('cms.index')->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        foreach (['image', 'image2', 'image3', 'image4', 'image5'] as $field) {
            if ($post->$field) {
                Storage::disk('public')->delete($post->$field);
            }
        }

        $post->delete();

        return redirect()->route('cms.index')->with('success', 'Post deleted successfully.');
    }

    private function validatedPost(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'place' => ['required', 'string', 'max:255'],
            'text_content' => ['required', 'string'],
            'date' => ['required', 'date'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'image' => ['nullable', 'image'],
            'image2' => ['nullable', 'image'],
            'image3' => ['nullable', 'image'],
            'image4' => ['nullable', 'image'],
            'image5' => ['nullable', 'image'],
        ]);

        $validated['video_timestamp'] = VideoRoute::timestamp(
            (float) $validated['latitude'],
            (float) $validated['longitude'],
            (float) config('video.duration'),
        );

        return $validated;
    }
}
