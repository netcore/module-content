@extends($page->layout)
@section('content')

    @foreach($page->contentBlocks->sortBy('order') as $contentBlock)

        @php
        $frontendTemplate = $contentBlock->config->frontend_template;
        @endphp

        @include($frontendTemplate, [
            'contentBlock' => $contentBlock
        ])
    @endforeach

@endsection
