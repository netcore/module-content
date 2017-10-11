@php
    $name = isset($name) ? $name : null;
    $value = isset($value) ? $value : null;
@endphp

@if(count($languages) > 1)
    @include('crud::nav_tabs', [
        'idPrefix' => 'simple-text-'
    ])
@endif

<!-- Tab panes -->
<div class="tab-content">
    @foreach($languages as $language)
        <div role="tabpanel" class="tab-pane {{ $loop->first ? 'active' : '' }}" id="simple-text-{{ $language->iso_code }}">

            <div class="form-group no-margin-bottom">
                <div class="">
                    {!! Form::textarea(
                        $name,
                        $value,
                        ['class' => 'summernote']
                    ) !!}
                </div>
            </div>

        </div>
    @endforeach
</div>
