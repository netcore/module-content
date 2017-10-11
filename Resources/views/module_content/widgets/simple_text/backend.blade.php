
@if(count($languages) > 1)
    @include('crud::nav_tabs', [
        'idPrefix' => 'simple-text-'
    ])
@endif

<!-- Tab panes -->
<div class="tab-content">
    @foreach($languages as $language)
        <div role="tabpanel" class="tab-pane {{ $loop->first ? 'active' : '' }}" id="simple-text-{{ $language->iso_code }}">

            @php
                $name = isset($name) ? $name : null;
                $value = array_get($translations, $language->iso_code . '.content');
            @endphp

            <div class="form-group no-margin-bottom">
                <div class="">
                    {!! Form::textarea(
                        $name,
                        $value,
                        [
                            'class' => 'summernote',
                            'data-language' => $language->iso_code
                        ]
                    ) !!}
                </div>
            </div>

        </div>
    @endforeach
</div>
