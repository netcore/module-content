
@php
    $imageBlockId = isset($imageBlock) ? $imageBlock->id : 'newid';
    $viewHelper = new \Modules\Content\Widgets\BackendViewHelpers\ImageBlock;
@endphp

<table
    class="table table-bordered image-blocks-table"
    data-image-block-id="{{ $imageBlockId }}"
>
    <thead>
    <tr>
        <th class="width-50">Reorder</th>
        @foreach($fields as $field)

            @php
                $classes = [];
                $styles = [];
                $fieldName = array_get($field, 'name');
                $fieldStyles = array_get($field, 'styles', []);
                $imageWidth = array_get($fieldStyles, 'image_width', 75);
                if($fieldName == 'image' AND count($fields) > 1) {
                    $styles[] = 'width:' . $imageWidth . 'px;';
                }

                $thWidth = array_get($fieldStyles, 'th_width');
                if($thWidth){
                    $styles[] = 'width:' . $thWidth. ';';
                }
            @endphp

            <th class="{{ join($classes, ' ') }}" style="{{ join($styles, '') }}">
                {{ ucfirst(array_get($field, 'label')) }}
            </th>
        @endforeach
        <th class="text-align-center width-150">Actions</th>
    </tr>
    </thead>
    <tbody>
    @if(isset($imageBlock))
        @foreach( $imageBlock->items->sortBy('order') as $model )
            <tr
                    class="fade-out-{{ $model->id }} image-blocks-tr"
                    data-image-block-item-id="{{ $model->id }}"
            >
                <td class="cursor-dragndrop image-blocks-handle text-align-center vertical-align-middle">
                    <span class="fa fa-icon fa-arrows"></span>
                </td>
                @foreach($fields as $field)

                    @php
                        $fieldName = array_get($field, 'name');
                        $fieldType = array_get($field, 'type');
                        $fieldValue = array_get($field, 'value');
                        $dataValue = $viewHelper->getDataValueForTd($model, $fieldName, $languages);

                        $fieldStyles = array_get($field, 'styles', []);
                        $imageWidth = array_get($fieldStyles, 'image_width', 75);
                    @endphp

                    <td
                        class="field {{ $fieldName == 'image' ? 'has-image' : '' }}"
                        data-value="{{ $dataValue }}"
                        data-field="{{ $fieldName }}"
                        data-td-id="{{ $loop->parent->index }}-image"
                    >
                        @if($fieldName == 'image')
                            @if($model->image_file_name)
                                <img
                                    src="{{ $model->image->url() }}"
                                    alt="Image"
                                    class="img-responsive"
                                    style="width:{{ $imageWidth }}px;"
                                >
                            @else
                                No image
                            @endif
                        @else
                            @foreach($languages as $language)

                                @php
                                    $value = $viewHelper->getValueForTd($model, $language, $fieldName);
                                @endphp

                                @if(count($languages) > 1)
                                    {{ strtoupper($language->iso_code) }}:
                                @endif

                                @if($fieldType == 'textarea')
                                    {!! $value !!}
                                @else
                                    {{ $value }}
                                @endif

                            @endforeach
                        @endif
                    </td>
                @endforeach
                <td class="text-align-center vertical-align-middle width-150">
                    @include('content::module_content.widgets.image_blocks.backend_actions', [
                        'modelId' => $model->id
                    ])
                </td>
            </tr>
        @endforeach

        @if( !$imageBlock->items->count() )
            <tr class="no-blocks-tr">
                <td colspan="100" class="text-align-center">
                    No items added...
                </td>
            </tr>
        @endif
    @else
        <tr class="no-blocks-tr">
            <td colspan="100" class="text-align-center">
                No items added...
            </td>
        </tr>
    @endif
    </tbody>
</table>

<script type="text/template" id="image-block-actions-template">
    @include('content::module_content.widgets.image_blocks.backend_actions', [
        'modelId' => 'modelId'
    ])
</script>
