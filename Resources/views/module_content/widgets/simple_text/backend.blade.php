
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
                            'class'       => 'summernote',
                            'data-field'  => 'content',
                            'data-locale' => $language->iso_code,
                            'maxlength'   => '8000000'
                        ]
                    ) !!}

                    <span class="error-span" data-field="{{ $language->iso_code }}-content"></span>
                </div>
            </div>

            @if(count($fields))
            <table>
                @foreach($fields as $field)
                    @php
                        $fieldName = array_get($field, 'name');
                        $fieldType = array_get($field, 'type');
                        $fieldLabel = array_get($field, 'label');

                        $fieldValue = array_get($translations, $language->iso_code . '.' . $fieldName);
                    @endphp
                    <tr>
                        <td class="text-align-right">
                            {{ ucfirst($fieldLabel) }}
                            @if(count($languages) > 1)
                                {{ strtoupper($language->iso_code) }}
                            @endif
                        </td>
                        <td class="padding-5">
                            <div class="form-group no-margin">
                                @if($fieldType == 'checkbox')
                                    <input
                                        type="checkbox"
                                        value="1"
                                        {{ $fieldValue ? 'checked' : '' }}
                                        data-field="{{ $fieldName }}"
                                        data-locale="{{ $language->iso_code }}"
                                        class=""
                                    >
                                @else
                                    <input
                                        type="text"
                                        maxlength="191"
                                        data-field="{{ $fieldName }}"
                                        data-locale="{{ $language->iso_code }}"
                                        class="form-control"
                                        value="{{ $fieldValue }}"
                                    >
                                @endif
                                <span class="error-span" data-field="{{ $language->iso_code }}-{{ $fieldName }}"></span>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </table>
            @endif

        </div>
    @endforeach
</div>
