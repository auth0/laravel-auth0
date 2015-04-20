@extends('layouts.master')

@section('content')

    <a href="{{ route('spa') }}">Login SPA (API + JWT)</a>

    @if(!$isLoggedIn)
    <a href="{{ route('login') }}">Login Oauth</a>
    @else
    <a href="{{ route('dump') }}">Dump user info</a>
    <a href="{{ route('logout') }}">Logout</a>
    @endif

@stop