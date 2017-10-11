@include('crud::nav_tabs')

<!-- Tab panes -->
<div class="tab-content">
    @foreach($languages as $language)
        <div role="tabpanel" class="tab-pane {{ $loop->first ? 'active' : '' }}" id="{{ $language->iso_code }}">

            <div class="row">
                <div class="col-xs-6">
                    <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                        <label>Title</label>
                        <div class="">
                            {!! Form::text('translations['.$language->iso_code.'][title]', trans_model((isset($entry) ? $entry : null), $language, 'title'), ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="form-group{{ $errors->has('slug') ? ' has-error' : '' }}">
                        <label>Slug</label>
                        (Automatically generated if left empty)
                        <div class="">
                            {!! Form::text('translations['.$language->iso_code.'][slug]', trans_model((isset($entry) ? $entry : null), $language, 'slug'), ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Content blocks --}}
            <div id="widgets-container">

                <div id="no-widgets" {{ $entry->contentBlocks->count() ? 'hidden' : '' }}>
                    Currently there is no content. Please add at least one block!
                </div>

                <table
                    class="table"
                    id="widgets-table"
                >
                    <tbody>
                        @foreach($entry->contentBlocks as $contentBlock)
                            @if($contentBlock->config->backend_template)
                                @php
                                    $templateData = [];
                                @endphp
                                @include('content::module_content.entries.partials.widget_tr_template', [
                                    'id'         => $contentBlock->id,
                                    'key'        => $contentBlock->config->key,
                                    'withBorder' => $contentBlock->config->backend_with_border ? 'with-border' : '',
                                    'template'   => view($contentBlock->config->backend_template, $templateData)->render()
                                ])
                            @else
                                @include('content::module_content.entries.partials.widget_tr_template', [
                                    'id'         => $contentBlock->id,
                                    'key'        => $contentBlock->config->key,
                                    'withBorder' => $contentBlock->config->backend_with_border ? 'with-border' : '',
                                    'template'   => $contentBlock->config->name
                                ])
                            @endif
                        @endforeach
                    </tbody>
                </table>

            </div>

            {!! Form::select(null, $widgetOptions, null, [
                'class' => 'form-control width-150 inline',
                'id' => 'select-widget'
            ]) !!}
            <a class="btn btn-xs btn-success" id="add-widget-button">Add widget</a>
        </div>
    @endforeach
</div>
