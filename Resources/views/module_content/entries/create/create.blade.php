@extends('admin::layouts.master')

@section('styles')
    @include('content::module_content.entries.form.styles')
@endsection

@section('scripts')
    <script src="{{ versionedAsset('assets/content/js/plugins/summernote-cleaner.js') }}"></script>
    @include('content::module_content.entries.form.scripts')
    <script src="{{ versionedAsset('assets/content/js/plugins/summernote-cleaner.js') }}"></script>
@endsection

@section('content')
    @include('admin::_partials._messages')

    {!! Form::open(['url' => content_crud_route('store', $channelId), 'method' => 'POST']) !!}
        <div class="p-x-1">
            @include('content::module_content.entries.form.form')
        </div>
    {!! Form::close() !!}
@endsection
