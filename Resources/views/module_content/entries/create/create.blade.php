@extends('admin::layouts.master')

@section('styles')
    @include('content::module_content.entries.form.styles')
@endsection

@section('scripts')
    @include('content::module_content.entries.form.scripts')
@endsection

@section('content')
    @include('admin::_partials._messages')

    {!! Form::open(['url' => content_crud_route('store', $channelId), 'method' => 'POST']) !!}
        <div class="p-x-1">
            @include('content::module_content.entries.form.form')
        </div>
    {!! Form::close() !!}
@endsection
