
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
onWidgetAdded['simple_text'] = function(widgetTr) {

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
};

/**
 *
 * Function that you put into widgetDataCollectors object
 * will be called to collect data from widget for usage in backend.
 *
 * widgetTr is <tr></tr> element that houses widget
 * 
 */
widgetDataCollectors['simple_text'] = function(widgetTr) {

    var translations = {};

    $(widgetTr).find('input[data-field], textarea[data-field]').each(function(index, object){

        var value = $(object).val();

        if($(object).is('input[type=checkbox]')) {
            value = $(object).is(':checked');
        }

        var field = $(object).data('field');

        var language = $(object).data('locale');

        if(translations[language] === undefined){
            translations[language] = {};
        }

        translations[language][field] = value;
    });

    return {
        'translations': translations
    };
};
