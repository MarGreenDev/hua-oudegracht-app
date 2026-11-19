@extends('layouts.app')

@section('content')
<div class="flex">
    <video src="{{asset('assets/videos/filmopname_oudegracht.mp4')}}"
           class="w-4xl m-4 mt-10"
           controls
           autoplay
           loop
    > </video>

    <div class="flex flex-col">
        <div class="flex-1">
            <h2>TESKSTVAK</h2>
        </div>

        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d541.8045001426383!2d5.1213025000093255!3d52.08974400673565!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1snl!2snl!4v1795089476347!5m2!1snl!2snl" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
    </div>
</div>
@endsection
