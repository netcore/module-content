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

    {!! Form::model($entry, ['url' => content_crud_route('update', $entry), 'method' => 'PUT']) !!}
        <div class="p-x-1" style="padding-bottom: 140px;">
            @include('content::module_content.entries.form.form')
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
