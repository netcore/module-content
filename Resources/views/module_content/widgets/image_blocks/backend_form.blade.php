<div class="add-new-container">

    <a class="btn btn-xs btn-success add-new-image-block-button pull-left">
        Add new block
    </a>

    <table class="add-new-image-block-table" hidden>
        @foreach($fields as $field)

            @php

                $fieldName = array_get($field, 'name');
                $fieldType = array_get($field, 'type');
                $fieldValue = array_get($field, 'value');
            @endphp

            @if($fieldName == 'image')
                <tr>
                    <td class="text-align-right">
                        {{ ucfirst($fieldName) }}
                    </td>
                    <td class="padding-5">
                        <div class="form-group no-margin">
                            <input type="file" data-name="html-block-images[]" data-field="{{ $fieldName }}" class="form-control form-input inline">
                            <span class="error-span"></span>
                        </div>
                    </td>
                </tr>
            @else
                @foreach($languages as $language)
                    <tr>
                        <td class="text-align-right">
                            {{ ucfirst($fieldName) }}
                            @if(count($languages) > 1)
                                {{ strtoupper($language->iso_code) }}
                            @endif
                        </td>
                        <td class="padding-5">
                            <div class="form-group no-margin">
                                @if($fieldType == 'textarea')
                                    <textarea
                                        data-field="{{ $fieldName }}"
                                        data-locale="{{ $language->iso_code }}"
                                        data-name="translations[{{ $fieldName }}][{{ $language->iso_code }}]"
                                        class="form-control summernote width-800"
                                    ></textarea>
                                @else
                                    <input
                                        type="text"
                                        data-field="{{ $fieldName }}"
                                        data-locale="{{ $language->iso_code }}"
                                        data-name="translations[{{ $fieldName }}][{{ $language->iso_code }}]"
                                        class="form-control"
                                    >
                                @endif
                                <span class="error-span"></span>
                            </div>
                        </td>
                    </tr>
                @endforeach
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
