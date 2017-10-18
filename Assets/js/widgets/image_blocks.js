
/**
 *
 * Function that you put into onWidgetAdded object
 * will be called every time widget of this type
 * is added
 *
 * It's a good place to initialise things like summernote,
 * switchery, bootstrap toolbars, etc.
 *
 * widgetTr is <tr></tr> element that houses widget
 *
 */
onWidgetAdded['image_blocks'] = function(widgetTr) {

    console.log('image_blocks widget added');
    /*
    $(widgetTr).find('.summernote').summernote({
        height: 300,
        focus: true,
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            //['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['insert', ['picture', 'link']]
        ]
    });
    */
};

/**
 *
 * Function that you put into widgetDataCollectors object
 * will be called to collect data from widget for usage in backend.
 *
 * widgetTr is <tr></tr> element that houses widget
 * 
 */
widgetDataCollectors['image_blocks'] = function(widgetTr) {

    return {};
    /*
    var translations = {};

    $(widgetTr).find('.summernote').each(function(index, object){
        var language = $(object).data('language');
        var content = $(object).val();

        translations[language] = {
            'content': content
        };
    });

    return {
        'translations': translations
    };
    */
};

(function(){
    console.log('Image block code');
})();
