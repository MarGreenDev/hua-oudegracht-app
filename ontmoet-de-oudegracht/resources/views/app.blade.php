@extends('layouts.app')

@section('content')
<div class="flex justify-between overflow-hidden">
    <video id="main-video"
           src="{{ route('video.stream') }}"
           class="w-full"
           preload="metadata"
           playsinline
    > </video>

    <div class="flex w-[500px] shrink-0 flex-col">
        <div id="post-text-area" class="h-[300px] overflow-y-auto flex-1">
            @if ($latestPost)
                <article>
                    <h2 class="text-2xl font-bold">{{ $latestPost->title }}</h2>

                    @if ($latestPost->place)
                        <p>{{ $latestPost->place }}</p>
                    @endif

                    @if ($latestPost->date)
                        <time datetime="{{ $latestPost->date }}">
                            {{ \Illuminate\Support\Carbon::parse($latestPost->date)->format('d-m-Y') }}
                        </time>
                    @endif

                    <p>{{ $latestPost->text_content }}</p>

                    @foreach (['image', 'image2', 'image3', 'image4', 'image5'] as $imageField)
                        @if ($latestPost->$imageField)
                            <img
                                src="{{ asset('storage/' . $latestPost->$imageField) }}"
                                alt=""
                                class="mt-2 max-h-48 w-full object-cover"
                            >
                        @endif
                    @endforeach
                </article>
            @else
                <h2>TESKSTVAK</h2>
            @endif
        </div>

        <p class="w-full overflow-hidden whitespace-nowrap" aria-hidden="true">
            @for ($i = 0; $i < 40; $i++)/ / @endfor
{{--            how to make dynamic??--}}
        </p>

        <div
            id="post-map"
            data-posts="{{ json_encode($posts) }}"
            data-video-route="{{ json_encode($videoRoute) }}"
            class="h-[300px] w-[500px]"
            aria-label="Map with post locations"
        ></div>
    </div>
</div>
@endsection
