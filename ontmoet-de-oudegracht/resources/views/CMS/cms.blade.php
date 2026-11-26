@extends('layouts.app')

@section('body-class', 'overflow-y-auto')

@section('content')
    @include('partials.logout')

    <div class="mx-auto max-w-6xl p-6">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold">Posts</h1>
                <p class="mt-1 text-gray-600">Manage the posts shown on the map.</p>
            </div>
            <button type="button" id="create-post-button" class="bg-theme-1 px-5 py-3 font-semibold text-white">
                New post
            </button>
        </div>

        @if (session('success'))
            <p class="mb-4 rounded bg-green-100 p-3 text-green-800" role="status">
                {{ session('success') }}
            </p>
        @endif

        @if ($posts->isEmpty())
            <p class="rounded border border-gray-200 p-6 text-gray-600">No posts yet. Create your first post to get started.</p>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $post)
                    <article class="flex flex-col overflow-hidden rounded border border-gray-200 bg-white shadow-sm">
                        @if ($post->image)
                            <img src="{{ asset('storage/' . $post->image) }}" alt="" class="h-48 w-full object-cover">
                        @endif
                        <div class="flex flex-1 flex-col p-4">
                            <h2 class="text-xl font-semibold">{{ $post->title }}</h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ $post->place }}@if ($post->date) · {{ $post->date }}@endif
                            </p>
                            <p class="mt-3 flex-1 text-gray-700">{{ \Illuminate\Support\Str::limit($post->text_content, 180) }}</p>
                            <div class="mt-4 flex gap-3">
                                <button
                                    type="button"
                                    class="edit-post-button border border-theme-1 px-4 py-2 text-theme-1"
                                    data-post="{{ json_encode($post) }}"
                                    data-update-url="{{ route('cms.posts.update', ['post' => '__POST__']) }}"
                                >
                                    Edit
                                </button>
                                <form action="{{ route('cms.posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Remove this post? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="border border-red-700 px-4 py-2 text-red-700">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>

    <dialog id="post-modal" class="m-auto max-h-[90vh] w-[min(42rem,calc(100%-2rem))] overflow-y-auto rounded-lg p-0 shadow-xl backdrop:bg-black/60">
        <div class="p-6">
            <div class="mb-5 flex items-center justify-between gap-4">
                <h2 id="post-modal-title" class="text-2xl font-bold">New post</h2>
                <button type="button" id="close-post-modal" class="px-3 py-2 text-gray-600" aria-label="Close dialog">Close</button>
            </div>

            @if ($errors->any())
                <div class="mb-4 rounded bg-red-100 p-3 text-red-800" role="alert">
                    <p class="font-semibold">Please correct the following:</p>
                    <ul class="list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                id="post-form"
                action="{{ route('cms.posts.store') }}"
                method="POST"
                enctype="multipart/form-data"
                data-store-url="{{ route('cms.posts.store') }}"
                class="flex flex-col gap-4"
            >
                @csrf
                <input type="hidden" name="_method" value="{{ old('post_id') ? 'PATCH' : '' }}">
                <input type="hidden" name="post_id" value="{{ old('post_id') }}">

                <label class="flex flex-col gap-1">
                    Title
                    <input type="text" name="title" value="{{ old('title') }}" required maxlength="255" class="border p-2">
                </label>

                <label class="flex flex-col gap-1">
                    Place
                    <input type="text" name="place" value="{{ old('place') }}" required maxlength="255" class="border p-2">
                </label>

                <label class="flex flex-col gap-1">
                    Text content
                    <textarea name="text_content" rows="6" required class="border p-2">{{ old('text_content') }}</textarea>
                </label>

                <label class="flex flex-col gap-1">
                    Date
                    <input type="date" name="date" value="{{ old('date') }}" required class="border p-2">
                </label>

                <div class="grid grid-cols-2 gap-4">
                    <label class="flex flex-col gap-1">
                        Latitude
                        <input type="number" name="latitude" value="{{ old('latitude') }}" step="0.0000001" min="-90" max="90" required class="border p-2">
                    </label>

                    <label class="flex flex-col gap-1">
                        Longitude
                        <input type="number" name="longitude" value="{{ old('longitude') }}" step="0.0000001" min="-180" max="180" required class="border p-2">
                    </label>
                </div>

                <label class="flex flex-col gap-1">
                    Video timestamp (seconds)
                    <input type="number" name="video_timestamp" value="{{ old('video_timestamp') }}" step="0.001" min="0" required class="border p-2">
                </label>

                @for ($i = 1; $i <= 5; $i++)
                    @php
                        $field = $i === 1 ? 'image' : 'image' . $i;
                    @endphp

                    <label class="flex flex-col gap-1">
                        Image {{ $i }}
                        <span id="{{ $field }}-preview" class="text-sm text-gray-600"></span>
                        <input type="file" name="{{ $field }}" accept="image/*" class="border p-2">
                    </label>
                @endfor

                <div class="mt-2 flex justify-end gap-3">
                    <button type="button" id="cancel-post-modal" class="border px-4 py-2">Cancel</button>
                    <button type="submit" id="post-submit-button" class="bg-theme-1 px-5 py-2 font-semibold text-white">
                        Create post
                    </button>
                </div>
            </form>
        </div>
    </dialog>

    <script>
        const postModal = document.querySelector('#post-modal');
        const postForm = document.querySelector('#post-form');
        const postFields = ['title', 'place', 'text_content', 'date', 'latitude', 'longitude', 'video_timestamp', 'image', 'image2', 'image3', 'image4', 'image5'];
        const imageFields = ['image', 'image2', 'image3', 'image4', 'image5'];
        const methodField = postForm.elements.namedItem('_method');
        const postIdField = postForm.elements.namedItem('post_id');

        function clearImagePreviews() {
            imageFields.forEach((field) => {
                document.querySelector(`#${field}-preview`).replaceChildren();
            });
        }

        function openCreatePost() {
            postForm.reset();
            postForm.action = postForm.dataset.storeUrl;
            postIdField.value = '';
            methodField.value = '';
            document.querySelector('#post-modal-title').textContent = 'New post';
            document.querySelector('#post-submit-button').textContent = 'Create post';
            clearImagePreviews();
            postModal.showModal();
        }

        function openEditPost(post, updateUrl) {
            postForm.reset();
            postForm.action = updateUrl.replace('__POST__', encodeURIComponent(post.id));
            postIdField.value = post.id;
            methodField.value = 'PATCH';
            postFields.forEach((field) => {
                if (!imageFields.includes(field)) {
                    postForm.elements.namedItem(field).value = post[field] ?? '';
                }
            });
            clearImagePreviews();
            imageFields.forEach((field) => {
                if (post[field]) {
                    const image = document.createElement('img');
                    image.src = `/storage/${post[field]}`;
                    image.alt = `Current ${field} image`;
                    image.className = 'mt-1 max-h-24 w-auto';
                    document.querySelector(`#${field}-preview`).append(image);
                }
            });
            document.querySelector('#post-modal-title').textContent = 'Edit post';
            document.querySelector('#post-submit-button').textContent = 'Save changes';
            postModal.showModal();
        }

        document.querySelector('#create-post-button').addEventListener('click', openCreatePost);
        document.querySelector('#close-post-modal').addEventListener('click', () => postModal.close());
        document.querySelector('#cancel-post-modal').addEventListener('click', () => postModal.close());

        document.querySelectorAll('.edit-post-button').forEach((button) => {
            button.addEventListener('click', () => {
                openEditPost(JSON.parse(button.dataset.post), button.dataset.updateUrl);
            });
        });

        @if ($errors->any())
            @if (old('post_id'))
                const oldPost = @json($posts->firstWhere('id', old('post_id')));
                if (oldPost) {
                    const updateUrl = @json(route('cms.posts.update', ['post' => '__POST__']));
                    postForm.action = updateUrl.replace('__POST__', encodeURIComponent(oldPost.id));
                    document.querySelector('#post-modal-title').textContent = 'Edit post';
                    document.querySelector('#post-submit-button').textContent = 'Save changes';
                }
            @endif
            postModal.showModal();
        @endif
    </script>
@endsection
