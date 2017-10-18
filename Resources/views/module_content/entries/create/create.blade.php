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
                        Create
                    </div>
                </div>
                <div class="panel-body">
                    @include('content::module_content.entries.form.form')
                </div>
            </div>

            <a
                class="btn btn-lg btn-success m-t-3 pull-xs-right disabled"
                {{--
                id="submit-button"
                --}}
            >
                Save
            </a>

            <a href="{{ route('content::content.index') }}" class="btn btn-lg btn-default m-t-3 m-r-1 pull-xs-right">
                Back
            </a>

        </div>
    {!! Form::close() !!}
@endsection
