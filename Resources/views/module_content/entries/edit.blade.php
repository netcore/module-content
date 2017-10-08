@extends('admin::layouts.master')

@section('styles')
    <style>

        .width-150{
            width: 150px;
        }

        .text-align-right{
            text-align: right;
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(function() {

            var widgets = {!! json_encode($widgetData) !!};

            $('body').on('click', '#add-widget-button', function(){
                var key = $('#select-widget option:selected').val();
                var data = widgets[key];
            });
        });
    </script>
@endsection

@section('content')
    @include('admin::_partials._messages')

    {!! Form::model($entry, ['url' => crudify_route('update', $entry), 'method' => 'PUT']) !!}

        <div class="p-x-1">

            @include('crud::nav_tabs')

            <!-- Tab panes -->
            <div class="tab-content">
                @foreach($languages as $language)
                    <div role="tabpanel" class="tab-pane {{ $loop->first ? 'active' : '' }}" id="{{ $language->iso_code }}">

                        <div class="row">
                            <div class="col-xs-6">
                                <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                    <label>Title</label>
                                    <div class="">
                                        {!! Form::text('translations['.$language->iso_code.'][title]', trans_model((isset($entry) ? $entry : null), $language, 'title'), ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group{{ $errors->has('slug') ? ' has-error' : '' }}">
                                    <label>Slug</label>
                                    (Automatically generated if left empty)
                                    <div class="">
                                        {!! Form::text('translations['.$language->iso_code.'][slug]', trans_model((isset($entry) ? $entry : null), $language, 'slug'), ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Content</label>
                            <div class="">
                                {!! Form::textarea('translations['.$language->iso_code.'][content]', trans_model((isset($entry) ? $entry : null), $language, 'content'), ['class' => 'summernote']) !!}
                            </div>
                        </div>

                        {!! Form::select(null, $widgetOptions, null, [
                            'class' => 'form-control width-150 inline',
                            'id' => 'select-widget'
                        ]) !!}
                        <a class="btn btn-xs btn-success" id="add-widget-button">Add widget</a>
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
