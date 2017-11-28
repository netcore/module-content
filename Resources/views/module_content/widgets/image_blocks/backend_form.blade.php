<div class="add-new-container"
     data-max-items-count="{{ $maxItemsCount }}"
>

    @php
        $itemsCount = isset($imageBlock) ? $imageBlock->items->count() : 0;
        $disabledAddNew = ($maxItemsCount && $itemsCount>=$maxItemsCount) ? true : false;
    @endphp

    <a
        class="btn btn-xs btn-success add-new-image-block-button pull-left {{ $disabledAddNew ? 'disabled' : '' }}"
    >
        Add new block

        @if($maxItemsCount)
            <span class="max-items-count">
                Max items count: {{ $maxItemsCount }}
            </span>
        @endif
    </a>

    <span class="clear:both;"></span>

    <table class="add-new-image-block-table" hidden>
        @foreach($fields as $field)

            @php
                $fieldName = array_get($field, 'name');
                $fieldLabel = array_get($field, 'label');
                $fieldType = array_get($field, 'type');
                $fieldValue = array_get($field, 'value');
                $fieldStyles = array_get($field, 'styles');
                $fieldOptions = (array) array_get($field, 'options');

                $imageWidth = array_get($fieldStyles, 'image_width');
                $notRequired = array_get($fieldStyles, 'not_required', 0);
            @endphp

            @if($fieldName == 'image')
                <tr>
                    <td class="text-align-right">
                        {{ ucfirst($fieldLabel) }}
                    </td>
                    <td class="padding-5">
                        <div class="form-group no-margin">
                            <input
                                type="file"
                                data-name="html-block-images[]"
                                data-field="{{ $fieldName }}"
                                class="form-control form-input inline"
                                data-image-width="{{ $imageWidth }}"
                                data-not-required="{{ $notRequired }}"
                            >
                            <span class="error-span"></span>
                        </div>
                    </td>
                </tr>
            @else
                {{--
                @foreach($languages as $language)
                --}}
                    <tr>
                        <td class="text-align-right">
                            {{ ucfirst($fieldLabel) }}
                            {{--
                            @if(count($languages) > 1)
                                {{ strtoupper($language->iso_code) }}
                            @endif
                            --}}
                        </td>
                        <td class="padding-5">
                            <div class="form-group no-margin">
                                @if($fieldType == 'textarea')
                                    <textarea
                                        maxlength="8000000"
                                        data-field="{{ $fieldName }}"
                                        data-locale="{{ $language->iso_code }}"
                                        data-not-required="{{ $notRequired }}"
                                        data-name="translations[{{ $fieldName }}][{{ $language->iso_code }}]"
                                        class="form-control image-blocks-summernote width-800"
                                    ></textarea>
                                @elseif($fieldType == 'checkbox')
                                    <input
                                        type="checkbox"
                                        value="1"
                                        data-field="{{ $fieldName }}"
                                        data-locale="{{ $language->iso_code }}"
                                        data-not-required="{{ $notRequired }}"
                                        data-name="translations[{{ $fieldName }}][{{ $language->iso_code }}]"
                                        class=""
                                    >
                                @elseif($fieldType == 'select')
                                    <select
                                        data-field="{{ $fieldName }}"
                                        data-locale="{{ $language->iso_code }}"
                                        data-not-required="{{ $notRequired }}"
                                        data-name="translations[{{ $fieldName }}][{{ $language->iso_code }}]"
                                        class="form-control"
                                    >
                                        @foreach($fieldOptions as $label => $value)
                                            <option {{ $value==$fieldValue ? 'checked' : '' }} value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input
                                        type="text"
                                        maxlength="191"
                                        data-field="{{ $fieldName }}"
                                        data-locale="{{ $language->iso_code }}"
                                        data-not-required="{{ $notRequired }}"
                                        data-name="translations[{{ $fieldName }}][{{ $language->iso_code }}]"
                                        class="form-control"
                                    >
                                @endif
                                <span class="error-span"></span>
                            </div>
                        </td>
                    </tr>
                {{--
                @endforeach
                --}}
            @endif
        @endforeach
        <tr>
            <td></td>
            <td class="padding-5 text-align-right">
                <a class="btn btn-xs btn-danger add-new-image-block-cancel">Cancel</a>
                <a class="btn btn-xs btn-success add-new-image-block-submit">Save</a>
            </td>
        </tr>
    </table>
</div>
