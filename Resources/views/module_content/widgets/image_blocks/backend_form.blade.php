
<a class="btn btn-xs btn-success add-new-image-block-button pull-left">
    Add new block
</a>

<table class="add-new-image-block-table" hidden>
    <tr>
        <td></td>
        <th class="padding-5">
            Add new:
        </th>
    </tr>
    @foreach($fields as $field => $value)
        @if($field != 'image')
            <tr>
                <td class="text-align-right">
                    {{ ucfirst($field) }}:
                </td>
                <td class="padding-5">
                    <div class="form-group no-margin">
                        <input type="text" data-name="{{ $field }}" class="form-control">
                    </div>
                </td>
            </tr>
        @endif
    @endforeach

    {{-- Image should be last of all fields --}}
    @foreach($fields as $field => $value)
        @if($field == 'image')
            <tr>
                <td class="text-align-right">
                    {{ ucfirst($field) }}:
                </td>
                <td class="padding-5">
                    <div class="form-group no-margin">
                        <input type="file" name="html-block-images[]" id="" class="form-control form-input inline">
                    </div>
                </td>
            </tr>
        @endif
    @endforeach
    <tr>
        <td></td>
        <td class="padding-5 text-align-right">
            <a class="btn btn-xs btn-success">Add</a>
        </td>
    </tr>
</table>
