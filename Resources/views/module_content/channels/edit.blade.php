@extends('admin::layouts.master')

@section('scripts')
    <script src="/assets/content/js/channels/form.js"></script>
@endsection

@section('styles')
    <link rel="stylesheet" href="/assets/content/css/channels/form.css">
@endsection

@section('content')

    {{--
    @include('admin::_partials._messages')
    --}}

    {!! Form::model($channel, ['url' => crud_route('update', $channel), 'method' => 'PUT']) !!}

        <div class="p-x-1">

            <div class="panel">
                <div class="panel-heading">
                    <div class="panel-title">
                        Edit channel
                    </div>
                </div>
                <div class="panel-body">
                    @include('content::module_content.channels.form')
                </div>
            </div>

            <button type="submit" class="btn btn-lg btn-success m-t-3 pull-xs-right">Save</button>

            <a href="{{ route('content::content.index') }}?channel={{ $channel->slug }}" class="btn btn-lg btn-default m-t-3 m-r-1 pull-xs-right">
                Back
            </a>

        </div>
    {!! Form::close() !!}
@endsection
