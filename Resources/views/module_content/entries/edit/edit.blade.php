@extends('admin::layouts.master')

@section('styles')
    @include('content::module_content.entries.form.styles')
@endsection

@section('scripts')
    @include('content::module_content.entries.form.scripts')
@endsection

@section('content')
    @include('admin::_partials._messages')

    {!! Form::model($entry, ['url' => content_crud_route('update', $entry), 'method' => 'PUT']) !!}

        <div class="p-x-1" style="padding-bottom: 140px;">

            <div class="panel">
                <div class="panel-heading">
                    <div class="panel-title">
                        Edit page
                    </div>
                </div>
                <div class="panel-body position-relative">
                    @include('content::module_content.entries.form.form')
                </div>
            </div>

        </div>
    {!! Form::close() !!}

    @if($entry->type == 'revision')

        {!! Form::model($entry, ['route' => ['content::entries.restore_revision', $entry], 'method' => 'POST']) !!}
            {!! Form::submit('Restore', [
                'class' => 'btn btn-lg btn-success m-t-3 m-r-1 pull-xs-right'
            ]) !!}
        {!! Form::close() !!}

        {{--
        {!! Form::model($entry, ['route' => ['content::entries.create_draft', $entry], 'method' => 'POST']) !!}
            {!! Form::submit('Create draft', [
                'class' => 'btn btn-lg btn-info m-t-3 m-r-1 pull-xs-right'
            ]) !!}
        {!! Form::close() !!}
        --}}

        <a href="{{ route('content::entries.edit', $entry->parent_id) }}" class="btn btn-lg btn-default m-t-3 m-r-1 pull-xs-right">
            Go to current version
        </a>
    @endif

@endsection
