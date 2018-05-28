@extends($page->layout)

@php
    $entryTranslation = $page->translateOrNew(app()->getLocale());
@endphp

@section('meta')
    @foreach($entryTranslation->metaTags as $metaTag)
        @if($metaTag->value)
            <meta @if($metaTag->property)property="{{ $metaTag->property }}"@endif @if($metaTag->name)name="{{ $metaTag->name }}"@endif content="{{ $metaTag->value }}">
        @endif
    @endforeach
@endsection

@section('content')
    @foreach($entryTranslation->contentBlocks->sortBy('order') as $contentBlock)

        @php
            $frontendTemplate = $contentBlock->config->frontend_template;
            $data = $contentBlock->data;
            $widgetFields = $contentBlock->items;
            $widgetBlockId = array_get($data, 'widget_block_id');
            $widgetBlock = \Modules\Content\Models\WidgetBlock::with('items.fields')->find($widgetBlockId);
        @endphp

        @includeIf($frontendTemplate, [
            'contentBlock' => $contentBlock,
            'widgetBlock' => $widgetBlock,
        ])
    @endforeach
@endsection
