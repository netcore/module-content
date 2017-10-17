
@if(count($languages) > 1)
    @include('content::module_content.partials.language_tabs')
@endif

<!-- Tab panes -->
<div class="tab-content {{ count($languages) > 1 ? '' : 'no-padding' }}">
    @foreach($languages as $language)
        <div role="tabpanel" class="tab-pane {{ $loop->first ? 'active' : '' }}" id="{{ $language->iso_code }}">

            <div class="row">
                <div class="col-xs-6">

                    @php
                        $fieldErrors = $errors->get('translations.'.$language->iso_code.'.name');
                    @endphp

                    <div class="form-group{{ $fieldErrors ? ' has-error' : '' }}">
                        <label>Name</label>
                        {!! Form::text('translations['.$language->iso_code.'][name]', trans_model((isset($channel) ? $channel : null), $language, 'name'), ['class' => 'form-control name']) !!}
                        @foreach($fieldErrors as $error)
                            <span class="error-span">
                                {{ $error }}
                            </span>
                        @endforeach
                    </div>
                </div>
                <div class="col-xs-6">

                    @php
                        $fieldErrors = $errors->get('translations.'.$language->iso_code.'.slug');
                    @endphp

                    <div class="form-group{{ $fieldErrors ? ' has-error' : '' }}">
                        <label>Slug</label>
                        (Automatically generated if left empty)
                        {!! Form::text('translations['.$language->iso_code.'][slug]', trans_model((isset($channel) ? $channel : null), $language, 'slug'), ['class' => 'form-control slug']) !!}
                        @foreach($fieldErrors as $error)
                            <span class="error-span">
                                {{ $error }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    @endforeach
</div>
