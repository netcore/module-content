@extends('admin::layouts.master')

@section('styles')
    @include('content::module_content.entries.form.styles')
@endsection

@section('scripts')
    @include('content::module_content.entries.form.scripts')
@endsection

@section('content')
    @include('admin::_partials._messages')

    {!! Form::open(['url' => crud_route('store', $channelId), 'method' => 'POST']) !!}

        <div class="p-x-1">

            <div class="panel">
                <div class="panel-heading">
                    <div class="panel-title">
                        Create {{ $channel ? $channel->name : 'Page' }}
                    </div>
                </div>
                <div class="panel-body position-relative">
                    @include('content::module_content.entries.form.form')
                </div>
            </div>

        </div>
    {!! Form::close() !!}
@endsection
