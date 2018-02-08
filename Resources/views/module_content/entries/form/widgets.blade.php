<div class="widgets-container" data-locale="{{ $language->iso_code }}">
    <div class="no-widgets"
    @if(isset($entry))
        {{ $entryTranslation->contentBlocks->count() ? 'hidden' : '' }}
            @endif
    >
        Currently there are no {{ strtoupper($language->iso_code) }} widgets added!
    </div>

    <table
            class="table widgets-table"
    >
        <tbody class="widgets-table-tbody">
        @if(isset($entry))
            @foreach($entryTranslation->contentBlocks->sortBy('order') as $contentBlock)

                @php
                    $template = $contentBlock->config->name;
                    if($contentBlock->config->backend_template) {
                        $template  = view(
                            $contentBlock->config->backend_template, $contentBlock->compose()->backend($language)
                        )->render();
                    }
                @endphp


                @include('content::module_content.entries.form.widget_tr_template', [
                    'contentBlock'   => $contentBlock,
                    'contentBlockId' => $contentBlock->id,
                    'key'            => $contentBlock->config->key,
                    'javascriptKey'  => $contentBlock->config->javascript_key,
                    'withBorder'     => $contentBlock->config->backend_with_border ? 'with-border' : '',
                    'template'       => $template,
                    'widgetName'     => $contentBlock->config->name,
                ])
            @endforeach
        @endif
        </tbody>
    </table>
</div>
