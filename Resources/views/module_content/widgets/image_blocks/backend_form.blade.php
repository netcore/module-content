<div class="add-new-container">

    <a class="btn btn-xs btn-success add-new-image-block-button pull-left">
        Add new block
    </a>

    <table class="add-new-image-block-table" hidden>
        @foreach($fields as $field => $value)
            @if($field == 'image')
                <tr>
                    <td class="text-align-right">
                        {{ ucfirst($field) }}:
                    </td>
                    <td class="padding-5">
                        <div class="form-group no-margin">
                            <input type="file" data-name="html-block-images[]" data-field="{{ $field }}" class="form-control form-input inline">
                            <span class="error-span"></span>
                        </div>
                    </td>
                </tr>
            @else
                @foreach($languages as $language)
                    <tr>
                        <td class="text-align-right">
                            {{ ucfirst($field) }}
                            @if(count($languages) > 1)
                                {{ strtoupper($language->iso_code) }}
                            @endif
                        </td>
                        <td class="padding-5">
                            <div class="form-group no-margin">
                                <input type="text" data-field="{{ $field }}" data-locale="{{ $language->iso_code }}" data-name="translations[{{ $field }}][{{ $language->iso_code }}]" class="form-control">
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
                <a class="btn btn-xs btn-success add-new-image-block-submit">Add</a>
            </td>
        </tr>
    </table>
</div>
