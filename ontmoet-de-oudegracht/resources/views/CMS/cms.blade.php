@extends('layouts.app')

@section('body-class', 'overflow-y-auto')

@section('content')
    <div class="mx-auto max-w-2xl p-6">
        <h1 class="mb-6 text-3xl font-bold">Create post</h1>

        @if (session('success'))
            <p class="mb-4 rounded bg-green-100 p-3 text-green-800">
                {{ session('success') }}
            </p>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded bg-red-100 p-3 text-red-800">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ url('/cms/posts') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
            @csrf

            <label class="flex flex-col gap-1">
                Title
                <input type="text" name="title" value="{{ old('title') }}" required class="border p-2">
            </label>

            <label class="flex flex-col gap-1">
                Place
                <input type="text" name="place" value="{{ old('place') }}" required class="border p-2">
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
                    <input type="file" name="{{ $field }}" accept="image/*" class="border p-2">
                </label>
            @endfor

            <button type="submit" class="bg-theme-1 p-3 text-white">
                Create post
            </button>
        </form>
    </div>
@endsection
