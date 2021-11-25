@extends('layouts.page_layout')

@section('header')
    @include('shared-pages.header')
@stop

@section('nav')
    @include('shared-pages.nav')
@stop

@section('body')
    @include('career-pages.career')
@stop

@section('footer')
    @include('shared-pages.footer')
@stop
