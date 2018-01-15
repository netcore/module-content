<div class="pull-left">
    {!! Form::select(null, $widgetOptions, null, [
        'class' => 'form-control width-150 inline',
        'id' => 'select-widget'
    ]) !!}

    <a class="btn btn-md btn-success" id="add-widget-button"><i class="fa fa-plus"></i> Add widget</a>
</div>

<div class="pull-right">

    <div
            class="form-group inline-block"
            style="margin-right:15px;"
    >
        {!! Form::text('published_at', (isset($entry) ? $entry->published_at->format('d.m.Y') : date('d.m.Y')), ['class' => 'form-control datepicker']) !!}
        <span class="error-span"></span>

    </div>
    <div
            class="form-group inline-block"
            style="margin-right:15px;"
    >

        @if(!$channel)
            Homepage?
            <span class="hidden-switchery" hidden style="margin-right:10px;">
             {!! Form::checkbox('is_homepage', 1, (isset($entry) ? null : 0), [
                 'class' => 'switchery'
             ]) !!}
                        </span>
        @endif
    </div>

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

    Active
    <span class="hidden-switchery" hidden>
        {!! Form::checkbox('is_active', 1, (isset($entry) ? null : 1), [
            'class' => 'switchery'
        ]) !!}
    </span>
</div>
