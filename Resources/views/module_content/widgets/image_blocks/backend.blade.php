
@if( $imageBlock && $imageBlock->items->count() )

    @include('content::module_content.widgets.image_blocks.backend_list')

    @include('content::module_content.widgets.image_blocks.backend_form')

@else
    <p style="margin-top:10px;">
        Currently there are no items added
    </p>
@endif



