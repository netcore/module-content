@extends('admin::layouts.master')

@section('scripts')
    <style>
    </style>
@endsection

@section('scripts')
    <script>
    </script>
@endsection

@section('content')
    @include('admin::_partials._messages')

    {!! Form::model($channel, ['url' => crudify_route('update', $channel), 'method' => 'PUT']) !!}

        <div class="p-x-1">

            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                <label>Name</label>
                <div class="">
                    {!! Form::text('name', null, ['class' => 'form-control']) !!}
                </div>
            </div>

            @include('crud::nav_tabs')

            <!-- Tab panes -->
            <div class="tab-content">
                @foreach($languages as $language)
                    <div role="tabpanel" class="tab-pane {{ $loop->first ? 'active' : '' }}" id="{{ $language->iso_code }}">

                        <div class="form-group{{ $errors->has('slug') ? ' has-error' : '' }}">
                            <label>Slug</label>
                            (Automatically generated if left empty)
                            <div class="">
                                {!! Form::text('translations['.$language->iso_code.'][slug]', trans_model((isset($channel) ? $channel : null), $language, 'slug'), ['class' => 'form-control']) !!}
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-lg btn-success m-t-3 pull-xs-right">Save</button>

            <a href="{{ route('content::content.index') }}" class="btn btn-lg btn-default m-t-3 m-r-1 pull-xs-right">
                Back
            </a>

        </div>
    {!! Form::close() !!}
@endsection
