@if(count($languages) > 1)
    @include('translate::_partials._nav_tabs', [
        'idPrefix' => 'basic-data-'
    ])
@endif

<div class="row">
    <div class="col-xs-12">

        <div class="form-group">
            <label>Date</label>
            {!! Form::text('published_at', (isset($entry) ? $entry->published_at->format('d.m.Y') : date('d.m.Y')), ['class' => 'form-control datepicker']) !!}
            <span class="error-span"></span>
        </div>

    </div>

    <div class="col-xs-6">

    </div>
</div>

<!-- Tab panes -->
<div class="tab-content {{ count($languages) > 1 ? '' : 'no-padding' }}">
    @foreach($languages as $language)
        <div role="tabpanel" class="tab-pane {{ $loop->first ? 'active' : '' }}" id="basic-data-{{ $language->iso_code }}">

            <div class="row">
                <div class="col-xs-4">
                    <div class="form-group">
                        <label>Title</label>
                        {!! Form::text('translations['.$language->iso_code.'][title]', trans_model((isset($entry) ? $entry : null), $language, 'title'), ['class' => 'form-control title']) !!}
                        <span class="error-span"></span>
                    </div>
                </div>
                <div class="col-xs-4">
                    <div class="form-group">
                        <label>Slug</label>
                        (Automatically generated if left empty)
                        {!! Form::text('translations['.$language->iso_code.'][slug]', trans_model((isset($entry) ? $entry : null), $language, 'slug'), ['class' => 'form-control slug']) !!}
                        <span class="error-span"></span>
                    </div>
                </div>
                <div class="col-xs-4">
                    <div class="form-group">
                        <label>Attachment</label>
                        <br>
                        {!! Form::file('attachment', [
                            'class' => 'form-control form-input',
                            'style' => ((isset($entry) && $entry->attachment_file_name) ? 'max-width:250px;display:inline;' : '' )
                        ]) !!}

                        <span class="error-span"></span>

                        @if(isset($entry) && $entry->attachment_file_name)
                            <a
                                class="btn btn-xs btn-danger confirm-action"
                                data-title="Confirmation"
                                data-text="Attachment will be deleted. Are you sure?"
                                data-confirm-button-text="Delete"
                                data-method="DELETE"
                                data-href="{{ route('content::entries.destroy_attachment', $entry) }}"
                                data-success-title="Success"
                                data-success-text="Attachment was deleted"
                                data-refresh-page-on-success
                            >
                                Delete
                            </a>

                            <a href="{{ $entry->attachment->url() }}" target="_blank">Download ({{ $entry->human_attachment_size }})</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Content blocks --}}
<div id="widgets-container">

    <div id="no-widgets"
        @if(isset($entry))
        {{ $entry->contentBlocks->count() ? 'hidden' : '' }}
        @endif
    >
        Currently there is no content. Please add at least one block!
    </div>

    <table
            class="table"
            id="widgets-table"
    >
        <tbody>
        @if(isset($entry))
            @foreach($entry->contentBlocks->sortBy('order') as $contentBlock)

                @php
                    $template = $contentBlock->config->name;
                    if($contentBlock->config->backend_template) {
                        $template  = view(
                            $contentBlock->config->backend_template, $contentBlock->compose()->backend()
                        )->render();
                    }
                @endphp

                @include('content::module_content.entries.form.widget_tr_template', [
                    'contentBlockId' => $contentBlock->id,
                    'key'            => $contentBlock->config->key,
                    'javascriptKey'  => $contentBlock->config->javascript_key,
                    'withBorder'     => $contentBlock->config->backend_with_border ? 'with-border' : '',
                    'template'       => $template,
                    'widgetName'     => $contentBlock->config->name
                ])
            @endforeach
        @endif
        </tbody>
    </table>

</div>

<div class="pull-left">
    {!! Form::select(null, $widgetOptions, null, [
        'class' => 'form-control width-150 inline',
        'id' => 'select-widget'
    ]) !!}

    <a class="btn btn-xs btn-success" id="add-widget-button">Add widget</a>
</div>

<div class="pull-right">

    @php
        $multipleLayouts = count($layoutOptions) > 1;;
    @endphp

    <div
        class="form-group {{ $multipleLayouts ? 'inline-block' : '' }}"
        style="margin-right:15px;"
        {{ $multipleLayouts ? '' : 'hidden' }}
    >
        {!! Form::select('layout', $layoutOptions, null, ['class' => 'form-control', 'style' => 'width:125px;']) !!}
        <span class="error-span"></span>
    </div>

    @if(!$channel)
        Homepage?
        <span class="hidden-switchery" hidden style="margin-right:10px;">
            {!! Form::checkbox('is_homepage', 1, (isset($entry) ? null : 0), [
                'class' => 'switchery'
            ]) !!}
        </span>
    @endif

    Active
    <span class="hidden-switchery" hidden>
        {!! Form::checkbox('is_active', 1, (isset($entry) ? null : 1), [
            'class' => 'switchery'
        ]) !!}
    </span>
</div>
