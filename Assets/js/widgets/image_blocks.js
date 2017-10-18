
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

    var randomString = function() {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for (var i = 0; i < 5; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    };

    var clearAddNewImageBlockForm = function(btn){
        // Revert "Add" button
        $(btn).closest('.add-new-container').find('.add-new-image-block-button').show();

        // Empty input fields
        $(btn).closest('.add-new-container').find('.add-new-image-block-table input').each(function(index, input){
            $(input).val(null);
        });

        // Hide form
        $(btn).closest('.add-new-container').find('.add-new-image-block-table').hide();
    };

    var replaceAll = function(search, replacement, source) {
        return source.split(search).join(replacement);
    };

    var addNewRow = function(btn){

        var modelId = randomString();

        // Add new row
        var html = '<tr class="fade-out-' + modelId + '" data-id="' + modelId + '">';

        var actionsTemplate = $('#image-block-actions-template').html();
        actionsTemplate = replaceAll('modelId', modelId, actionsTemplate);

        // Handler for dragndrop
        html += '<td class="text-align-center vertical-align-middle">';
        html += '<span class="fa fa-icon fa-arrows"></span>';
        html += '</td>';

        // For each input - one td
        $(btn).closest('.add-new-container').find('input').each(function(index, input){
            var value = $(input).val();
            var type = $(input).attr('type');

            html += '<td>';

            if( $.inArray(type, ['text', 'number', 'textarea']) !== -1 ) {
                html += value;
            } else if( type === 'file' && value ) {
                var src = URL.createObjectURL( input.files[0] );
                html += '<img class="img-responsive width-50" src="' + src + '">';
            }

            html += '</td>';
        });

        // Actions
        html += '<td class="text-align-center vertical-align-middle">';
        html += actionsTemplate;
        html += '</td>';

        html += '</tr>';

        $(btn).closest('.template-container-body').find('.image-blocks-table tr:last').after(html);
    };

    $('body').on('click', '.add-new-image-block-button', function(){
        $(this).hide();
        $(this).closest('.add-new-container').find('.add-new-image-block-table').fadeIn();
    });

    $('body').on('click', '.add-new-image-block-cancel', function(){
        clearAddNewImageBlockForm(this);
    });

    $('body').on('click', '.add-new-image-block-submit', function(){

        var valid = true;
        $(this).closest('.add-new-container').find('input').each(function(index, input){
            var value = $(input).val();
            if(!value) {
                $(input).closest('.form-group').addClass('has-error');
                $(input).closest('.form-group').find('.error-span').text('Field is required');
                valid = false;
            } else {
                $(input).closest('.form-group').removeClass('remove-error');
                $(input).closest('.form-group').find('.error-span').text('');
            }
        });

        if(!valid){ return; }

        addNewRow(this);
        clearAddNewImageBlockForm(this);
    });
})();
