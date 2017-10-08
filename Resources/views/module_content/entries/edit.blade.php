@extends('admin::layouts.master')

@section('scripts')
    <style>
    </style>
@endsection

@section('scripts')
    <script>
        /*
        $('.datepicker').datepicker();
        $(function() {
            $('.ritch-textarea').summernote({
                height: 200,
                toolbar: [
                    ['parastyle', ['style']],
                    ['fontstyle', ['fontname', 'fontsize']],
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['insert', ['picture', 'link', 'video', 'table', 'hr']],
                    ['history', ['undo', 'redo']],
                    ['misc', ['codeview', 'fullscreen']]
                ],
            });
        });
        */
    </script>
@endsection

@section('content')
    @include('admin::_partials._messages')

    {!! Form::model($entry, ['url' => crudify_route('update', $entry), 'method' => 'PUT']) !!}

        <div class="p-x-1">
            {{--
            --}}

            @php
            $fields = ['name' => 'text'];
            @endphp

            @foreach( $fields as $field => $type )
                <fieldset class="form-group form-group-lg{{$errors->has($field) ? ' form-message-light has-error has-validation-error' : ''}}">
                    <label for="{{$field}}">{{title_case(str_replace('_', ' ', $field))}}</label>

                    <?php
                    $attributes = ['id' => $field, 'class' => 'form-control', 'autocomplete' => 'off' ];

                    if( $type == 'password' ){
                        echo Form::$type($field, $attributes);
                    }

                    else if( in_array($type, ['boolean','select']) ){
                        echo Form::select($field,[1 => 'yes', 2 => 'no'], null, $attributes);
                    }
                    else if($type == 'ritchtext') {
                        $attributes['class'] = 'form-control ritch-textarea';

                        echo Form::textarea($field, null, $attributes);
                    }
                    else {
                        echo Form::$type($field, null, $attributes);
                    }
                    ?>
                    @if ($errors->has($field))
                        <div id="validation-message-light-error" class="form-message validation-error">
                            @foreach ($errors->get($field) as $message)
                                {{$message}} <br>
                            @endforeach
                        </div>
                    @endif
                </fieldset>
            @endforeach

            @include('crud::nav_tabs')

            <!-- Tab panes -->
            <div class="tab-content">
                @foreach($languages as $language)
                    <div role="tabpanel" class="tab-pane {{ $loop->first ? 'active' : '' }}" id="{{ $language->iso_code }}">

                        <div class="form-group{{ $errors->has('slug') ? ' has-error' : '' }}">
                            <label>Slug</label>
                            <div class="">
                                {!! Form::text('translations['.$language->iso_code.'][slug]', trans_model((isset($entry) ? $entry : null), $language, 'slug'), ['class' => 'form-control']) !!}
                                <span class="help-block">
                                    If the field is left empty, slug will be generated automatically
                                </span>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('content') ? ' has-error' : '' }}">
                            <label>Content</label>
                            <div class="">
                                {!! Form::textarea('translations['.$language->iso_code.'][content]', trans_model((isset($entry) ? $entry : null), $language, 'content'), ['class' => 'summernote']) !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>


























            <button type="submit" class="btn btn-lg btn-success m-t-3 pull-xs-right">Save</button>

            <a href="{{ route('content::content.index') }}" class="btn btn-lg btn-default m-t-3 m-r-1 pull-xs-right">
                Back
            </a>

            {{--<a href="javascript:;" class=" text-muted p-t-4">Deactivate resource</a>--}}
        </div>
    {!! Form::close() !!}
@endsection
