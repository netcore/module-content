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
</script>

@foreach($widgetData as $data)
    @if( array_get($data, 'backend_javascript') )
        <script src="/assets/content/js/widgets/{{ array_get($data, 'backend_javascript') }}"></script>
    @endif
@endforeach

<script id="widget-tr-template" type="text/template">
        @include('content::module_content.entries.form.widget_tr_template')
</script>

<script src="/assets/content/js/entries/form.js"></script>
