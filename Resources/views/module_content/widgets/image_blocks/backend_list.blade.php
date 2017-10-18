<table
    class="table table-bordered image-blocks-table"
>
    <thead>
    <tr>
        <th>Reorder</th>
        @foreach($fields as $field => $value)
            <th {{ $field == 'image' ? 'text-align-center' : '' }}>{{ ucfirst($field) }}</th>
        @endforeach
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    @foreach( $imageBlock->items->sortBy('order') as $model )
        <tr
                class="fade-out-{{ $model->id }} image-blocks-tr"
                data-id="{{ $model->id }}"
        >
            <td class="cursor-dragndrop image-blocks-handle text-align-center vertical-align-middle width-50">
                <span class="fa fa-icon fa-arrows"></span>
            </td>
            @foreach($fields as $field => $value)

                @php
                    $value = '';
                    if($field != 'image') {
                        $value = [];
                        foreach($languages as $language) {
                            $value[$language->iso_code] = trans_model($model, $language, $field);
                        }
                        $value = json_encode($value);
                    }
                @endphp

                @if($field == 'image')
                    <td class="text-align-center width-75" data-value="{{ $value }}" data-field="{{ $field }}">
                        @if($model->image)
                            <img
                                src="{{ $model->image->url() }}"
                                alt="Image"
                                class="img-responsive width-50"
                            >
                        @endif
                    </td>
                @else
                    <td class="field" data-value="{{ $value }}" data-field="{{ $field }}">
                        @foreach($languages as $language)
                            @if(count($languages) > 1)
                                {{ strtoupper($language->iso_code) }}:
                            @endif
                            {{ trans_model($model, $languages->first(), $field) }}
                        @endforeach
                    </td>
                @endif
            @endforeach
            <td style="text-align:center;vertical-align:middle;">
                @include('content::module_content.widgets.image_blocks.backend_actions', [
                    'modelId' => $model->id
                ])
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<script type="text/template" id="image-block-actions-template">
    @include('content::module_content.widgets.image_blocks.backend_actions', [
        'modelId' => 'modelId'
    ])
</script>
