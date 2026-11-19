@extends('layouts.app')

@section('content')
    <h1 class="text-7xl">DIT IS DE CMS LOGIN!!</h1>

    @if($errors->any())
        <p>{{$errors->first()}}</p>
    @endif

    @include('partials.login-form')
@endsection
