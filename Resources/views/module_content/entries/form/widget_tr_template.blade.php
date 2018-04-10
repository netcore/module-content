
@php
    $contentBlockId = isset($contentBlockId) ? $contentBlockId : '{{ contentBlockId }}';
    $key            = isset($key) ? $key : '{{ key }}';
    $javascriptKey  = isset($javascriptKey) ? $javascriptKey : '{{ javascriptKey }}';
    $withBorder     = isset($withBorder) ? $withBorder : '{{ withBorder }}';
    $template       = isset($template) ? $template : '{{ template }}';
    $widgetName     = isset($widgetName) ? $widgetName : '{{ widgetName }}';
@endphp

<tr
    data-content-block-id="{{ $contentBlockId }}"
    data-key="{{ $key }}"
    data-javascript-key="{{ $javascriptKey }}"
    class="widget-tr"
>
    <td style="padding-bottom: 30px">
        <div class="template-container {{ $withBorder }}">

            <div class="template-container-header ">
                <span class="fa fa-icon fa-arrows cursor-dragndrop widget-tr-handle"></span>

                {{--<span style="left: {{ (count($languages)-1) * 90 }}px; position:relative;" class="no-drag">--}}
                <span style=" position:relative;" class="no-drag">
                    <span class="panel-title">{{ $widgetName }}</span>

                    <div class="panel-heading-controls">

                        <div class="btn-group btn-group-xs">
                            <a class="btn btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-gear"></i></a>

                            <ul class="dropdown-menu dropdown-menu-right">
                                {{--<li><a href="#">Collapse</a></li>--}}
                                {{--<li><a href="#">Disable</a></li>--}}
                                <li class="divider"></li>
                                <li><a href="javascript:;" class="delete-widget"><i class="fa fa-trash"></i> Delete</a></li>
                            </ul>
                        </div>
                    </div>

                </span>

            </div>

            <div class="template-container-body">
                {!! $template !!}
            </div>

        </div>
    </td>
</tr>
