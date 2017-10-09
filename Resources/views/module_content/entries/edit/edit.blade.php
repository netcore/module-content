@extends('admin::layouts.master')

@section('styles')
    <link rel="stylesheet" href="/assets/content/css/entries/edit.css">
@endsection

@section('scripts')

    <script>
        /**
         *
         * This object is populated with callback functions that should be
         * executed after certain types of widgets are added.
         *
         * It gets populated down below where we include javascripts from $data['javascript']
         *
        */
        var onWidgetAdded = {};
    </script>

    @foreach($widgetData as $data)
        @if(isset($data['javascript']))
            <script src="/assets/content/js/widgets/{{ $data['javascript'] }}"></script>
        @endif
    @endforeach

    <script src="/assets/content/js/entries/edit.js"></script>
@endsection

@section('content')
    @include('admin::_partials._messages')

    {!! Form::model($entry, ['url' => crudify_route('update', $entry), 'method' => 'PUT']) !!}

        <div class="p-x-1">

            <div class="panel">
                <div class="panel-heading">
                    <div class="panel-title">
                        Edit {{ $entry->title }}
                    </div>
                </div>
                <div class="panel-body">
                    @include('content::module_content.entries.edit.panel')
                </div>
            </div>

            <button type="submit" class="btn btn-lg btn-success m-t-3 pull-xs-right">Save</button>

            <a href="{{ route('content::content.index') }}" class="btn btn-lg btn-default m-t-3 m-r-1 pull-xs-right">
                Back
            </a>

        </div>
    {!! Form::close() !!}
@endsection
