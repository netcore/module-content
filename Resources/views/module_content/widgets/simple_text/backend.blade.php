
@if(count($languages) > 1)
    @include('crud::nav_tabs', [
        'idPrefix' => 'simple-text-'
    ])
@endif

<!-- Tab panes -->
<div class="tab-content {{ count($languages) <= 1 ? 'no-padding' : '' }}">
    @foreach($languages as $language)
        <div role="tabpanel" class="tab-pane {{ $loop->first ? 'active' : '' }}" id="simple-text-{{ $language->iso_code }}">

            @php
                $name = isset($name) ? $name : null;
                $value = array_get($translations, $language->iso_code . '.content');
            @endphp

            <div class="form-group no-margin-bottom">
                <div class="">
                    {{--
                    Why do we have "hidden"?
                    Otherwise client can see raw html in textarea before summernoet kicks in.
                    --}}
                    {!! Form::textarea(
                        $name,
                        $value,
                        [
                            'hidden',
                            'class' => 'summernote',
                            'data-language' => $language->iso_code
                        ]
                    ) !!}

                    <span class="error-span" data-field="{{ $language->iso_code }}-content"></span>
                </div>
            </div>

        </div>
    @endforeach
</div>
