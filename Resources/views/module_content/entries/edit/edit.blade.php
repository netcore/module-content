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
         * These are typically used for things like switchery, bootstrap tooltips, wysiwyg editors
         *
         * Object gets populated down below where we include javascripts from $data['javascript']
        */
        var onWidgetAdded = {};

        /**
         *
         * This object is populated with functions that known
         * how to collect data from widgets for usage in backend.
         *
         * Object gets populated down below where we include javascripts from $data['backend_javascript']
         */
        var widgetDataCollectors = {};
    </script>

    @foreach($widgetData as $data)
        @if( array_get($data, 'backend_javascript') )
            <script src="/assets/content/js/widgets/{{ array_get($data, 'backend_javascript') }}"></script>
        @endif
    @endforeach

    <script id="widget-tr-template" type="text/template">
        @include('content::module_content.entries.partials.widget_tr_template')
    </script>

    <script src="/assets/content/js/entries/edit.js"></script>
@endsection

@section('content')
    @include('admin::_partials._messages')

    {!! Form::model($entry, ['url' => crud_route('update', $entry), 'method' => 'PUT']) !!}

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

            <a
                class="btn btn-lg btn-success m-t-3 pull-xs-right"
                id="submit-button"
                data-ajax="{{ route('content::entries.update', $entry) }}"
                data-method="PUT"
            >
                Save
            </a>

            <a href="{{ route('content::content.index') }}" class="btn btn-lg btn-default m-t-3 m-r-1 pull-xs-right">
                Back
            </a>

        </div>
    {!! Form::close() !!}
@endsection
