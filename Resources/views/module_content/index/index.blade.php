@extends('admin::layouts.master')

@section('styles')
    <link rel="stylesheet" href="/assets/content/css/index/index.css">
@endsection

@section('scripts')
    <script src="/assets/content/js/index/index.js"></script>
@endsection

@section('content')
    <ol class="breadcrumb page-breadcrumb">
        <li><a href="{{url('/admin')}}">Admin</a></li>
        <li>
            <a href="{{crud_route('index')}}">
                Content
            </a>
        </li>
    </ol>

    <div class="page-header">
        <h1>
            <span class="text-muted font-weight-light">
                <i class="page-header-icon ion-ios-keypad"></i>
                Content
            </span>
        </h1>
    </div>

    @include('admin::_partials._messages')

    <div id="nav-tabs-container">
        @include('content::module_content.index.nav_tabs')
    </div>

@endsection
