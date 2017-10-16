
@if(count($languages) > 1)
    @include('crud::nav_tabs')
@endif

<!-- Tab panes -->
<div class="tab-content {{ count($languages) > 1 ? '' : 'no-padding' }}">
    @foreach($languages as $language)
        <div role="tabpanel" class="tab-pane {{ $loop->first ? 'active' : '' }}" id="{{ $language->iso_code }}">

            <div class="row">
                <div class="col-xs-6">
                    <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                        <label>Title</label>
                        {!! Form::text('translations['.$language->iso_code.'][title]', trans_model((isset($entry) ? $entry : null), $language, 'title'), ['class' => 'form-control title']) !!}
                        <span class="error-span"></span>
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="form-group{{ $errors->has('slug') ? ' has-error' : '' }}">
                        <label>Slug</label>
                        (Automatically generated if left empty)
                        {!! Form::text('translations['.$language->iso_code.'][slug]', trans_model((isset($entry) ? $entry : null), $language, 'slug'), ['class' => 'form-control slug']) !!}
                        <span class="error-span"></span>
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
            @foreach($entry->contentBlocks as $contentBlock)

                @php
                    $template = $contentBlock->config->name;
                    if($contentBlock->config->backend_template) {
                        $template  = view(
                            $contentBlock->config->backend_template, $contentBlock->compose()->backend()
                        )->render();
                    }
                @endphp

                @include('content::module_content.entries.form.widget_tr_template', [
                    'id'         => $contentBlock->id,
                    'key'        => $contentBlock->config->key,
                    'withBorder' => $contentBlock->config->backend_with_border ? 'with-border' : '',
                    'template'   => $template
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
    Active
    <span class="hidden-switchery" hidden>
        {!! Form::checkbox('is_active', 1, (isset($entry) ? null : 1), [
            'class' => 'switchery'
        ]) !!}
    </span>
</div>
