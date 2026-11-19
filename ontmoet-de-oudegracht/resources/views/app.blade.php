@extends('layouts.app')

@section('content')

    <video src="{{asset('assets/videos/filmopname_oudegracht.mp4')}}"
           class="w-4xl m-4 mt-10"
           controls
           autoplay
           loop
    />

@endsection
