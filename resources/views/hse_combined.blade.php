@extends('layouts.page_layout')

@section('header')
    @include('shared-pages.header')
@stop

@section('nav')
    @include('shared-pages.nav')
@stop

@section('body')
    @include('hse-pages.hse')
@stop

@section('footer')
    @include('shared-pages.footer')
@stop
