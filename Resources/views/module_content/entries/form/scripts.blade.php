<script>

    /**
     *
     * This object is populated with callback functions that should be
     * executed after certain types of widgets are added.
     *
     * These are typically used for things like switchery, bootstrap tooltips, wysiwyg editors
     *
     * Object gets populated down below where we include javascripts from $data['javascript']
     */
    var onWidgetAdded = {};

    /**
     *
     * This object is populated with functions that known
     * how to collect data from widgets for usage in backend.
     *
     * Object gets populated down below where we include javascripts from $data['backend_javascript']
     */
    var widgetDataCollectors = {};

    /**
     *
     * This object contains images that will be sent to backend via FormData
     * It can be populated in Collectors
     *
    */
    var formDataImages = {};
</script>

@php
    $jsFiles = [];
    foreach($widgetData as $data) {
        $file = array_get($data, 'backend_javascript');
        if( $file && !in_array($file, $jsFiles) ) {
            $jsFiles[] = $file;
        }
    }
@endphp

@foreach($jsFiles as $file)
    <script src="/assets/content/js/widgets/{{ $file }}"></script>
@endforeach

<script id="widget-tr-template" type="text/template">
    @include('content::module_content.entries.form.widget_tr_template')
</script>

<script src="/assets/content/js/entries/form.js"></script>
