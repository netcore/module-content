
@php
    $id            = isset($id) ? $id : '{{ id }}';
    $key           = isset($key) ? $key : '{{ key }}';
    $javascriptKey = isset($javascriptKey) ? $javascriptKey : '{{ javascriptKey }}';
    $withBorder    = isset($withBorder) ? $withBorder : '{{ withBorder }}';
    $template      = isset($template) ? $template : '{{ template }}';
@endphp

<tr data-id="{{ $id }}" data-key="{{ $key }}" data-javascript-key="{{ $javascriptKey }}" class="widget-tr">
    <td>
        <div class="template-container {{ $withBorder }}">

            <div class="template-container-header cursor-dragndrop widget-tr-handle">
                <span class="fa fa-icon fa-arrows"></span>
                <a class="delete-widget" style="left: {{ (count($languages)-1) * 90 }}px;">Delete</a>
            </div>

            <div class="template-container-body">
                {!! $template !!}
            </div>

        </div>
    </td>
</tr>
