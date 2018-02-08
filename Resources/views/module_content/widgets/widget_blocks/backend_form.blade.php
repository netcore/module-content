<div class="add-new-container"
     data-max-items-count="{{ $maxItemsCount }}"
     style="margin-top: 30px; margin-left: 5px;"
>

    @php
        $itemsCount = isset($widgetBlock) ? $widgetBlock->items->count() : 0;
        $disabledAddNew = ($maxItemsCount && $itemsCount>=$maxItemsCount) ? true : false;
    @endphp

    <a
        class="btn btn-xs btn-success add-new-image-block-button pull-left {{ $disabledAddNew ? 'disabled' : '' }}"
    >
        <i class="fa fa-plus"></i> Add new block

        @if($maxItemsCount)
            <span class="max-items-count">
                Max items count: {{ $maxItemsCount }}
            </span>
        @endif
    </a>

    <span class="clear:both;"></span>

    <table class="add-new-image-block-table" hidden>
        @foreach($fields as $field)
            @include('content::module_content.partials._field')
        @endforeach
        <tr>
            <td></td>
            <td class="padding-5 text-align-right">
                <a class="btn btn-xs btn-danger add-new-image-block-cancel"><i class="fa fa-undo"></i> Cancel</a>
                <a class="btn btn-xs btn-success add-new-image-block-submit"><i class="fa fa-pencil"></i> Update</a>
            </td>
        </tr>
    </table>
</div>
