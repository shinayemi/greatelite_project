@extends('layouts.welcome_layout')

@section('header')
@include('welcome-pages.header')
@stop

@section('body')
@include('welcome-pages.password-reset')
@stop

@section('footer')
@include('welcome-pages.footer')
@stop