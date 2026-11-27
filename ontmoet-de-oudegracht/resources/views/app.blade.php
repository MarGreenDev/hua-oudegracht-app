@extends('layouts.app')

@section('body-class', 'experience-page')
@section('html-lang', 'nl')

@section('content')
<div class="experience-layout">
    <div class="experience-video-frame">
        <video
            id="main-video"
            src="{{ route('video.stream') }}"
            preload="metadata"
            playsinline
            aria-label="Video of the Oudegracht"
        ></video>
    </div>

    <div class="experience-sidebar">
        <div id="post-text-area" class="experience-story">
            @if ($latestPost)
                <article>
                    <h2 class="experience-post-title">{{ $latestPost->title }}</h2>

                    @if ($latestPost->place)
                        <p class="experience-post-meta">{{ $latestPost->place }}</p>
                    @endif

                    @if ($latestPost->date)
                        <time class="experience-post-meta" datetime="{{ $latestPost->date }}">
                            {{ \Illuminate\Support\Carbon::parse($latestPost->date)->format('d-m-Y') }}
                        </time>
                    @endif

                    <p class="experience-post-content">{{ $latestPost->text_content }}</p>

                    @foreach (['image', 'image2', 'image3', 'image4', 'image5'] as $imageField)
                        @if ($latestPost->$imageField)
                            <img
                                src="{{ asset('storage/' . $latestPost->$imageField) }}"
                                alt=""
                                class="experience-post-image"
                            >
                        @endif
                    @endforeach
                </article>
            @else
                <h2 class="experience-post-title">Ontdek de Oudegracht</h2>
                <p class="experience-post-content">Tik op een locatie op de kaart om een verhaal te ontdekken.</p>
            @endif
        </div>

        <div
            id="post-map"
            data-posts="{{ json_encode($posts) }}"
            data-video-route="{{ json_encode($videoRoute) }}"
            class="experience-map"
            aria-label="Kaart met verhalen langs de Oudegracht"
        ></div>
    </div>
</div>
@endsection
