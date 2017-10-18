
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

    var blocks = [];

    $(widgetTr).find('.image-blocks-tr').each(function(index, tr){

        var id = $(tr).data('id');
        var order = (index+1);
        var attributes = {};

        $(tr).find('td.field').each(function(i, td){

            var attribute = $(td).data('field');
            var value = $(td).data('value');

            attributes[attribute] = value;
        });

        blocks.push({
            'id': id,
            'order': order,
            'attributes': attributes
        });
    });

    return {
        'html_block_id': $(widgetTr).data('html_block_id'), // @TODO
        'blocks': blocks
    };
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

    var initSortable = function(table){

        // Orderable shop images
        $(table).sortable({
            containerSelector: '.image-blocks-table',
            itemPath : '> tbody',
            itemSelector : '.image-blocks-tr',
            handle : '.image-blocks-handle',
            onDrop : function($item, container, _super, event) {

                console.log('widgets-x drop');

                $item.removeClass(container.group.options.draggedClass).removeAttr("style");
                $("body").removeClass(container.group.options.bodyClass);

                var order = [];
                var id;

                $.each( $(container.el).find('tr'), function (i, tr) {
                    if( id = $(tr).data('id') ) {
                        order.push( id );
                    }
                });
            }
        });
    };

    // Init sortable on page load
    $('.image-blocks-table').each(function(index, table){
        initSortable(table);
    });

    var getFieldsJsonValue = function(btn){

        var translatableFields = [];
        var regularFields = [];
        $(btn).closest('.add-new-container').find('input').each(function(index, input){

            var field = $(input).data('field');

            var locale = $(input).data('locale');
            if(locale) {
                translatableFields.push(field);
            } else {
                regularFields.push(field);
            }
        });

        var fieldsJsonValue = {};

        $(translatableFields).each(function(i, field){

            var jsonValue = {};

            $(btn).closest('.add-new-container').find('input[data-field="' + field + '"]').each(function(i, input){

                var locale = $(input).data('locale');
                var field = $(input).data('field');
                var value = $(input).val();

                if( jsonValue[locale] === undefined ) {
                    jsonValue[locale] = {};
                }

                jsonValue[locale][field] = value;
            });

            fieldsJsonValue[field] = jsonValue;
        });

        $(regularFields).each(function(i, field){

            var jsonValue = {};

            $(btn).closest('.add-new-container').find('input[data-field="' + field + '"]').each(function(i, input){
                var value = $(input).val();
                jsonValue['value'] = value;
            });

            fieldsJsonValue[field] = jsonValue;
        });

        return fieldsJsonValue;
    };

    var addNewRow = function(btn){

        var modelId = randomString();

        // Add new row
        var html = '<tr class="fade-out-' + modelId + ' image-blocks-tr" data-id="' + modelId + '">';

        var actionsTemplate = $('#image-block-actions-template').html();
        actionsTemplate = replaceAll('modelId', modelId, actionsTemplate);

        // Handler for dragndrop
        html += '<td class="cursor-dragndrop image-blocks-handle text-align-center vertical-align-middle width-50">';
        html += '<span class="fa fa-icon fa-arrows"></span>';
        html += '</td>';

        // For each input - one td
        var fieldsJsonValue = getFieldsJsonValue(btn);

        $(btn).closest('.add-new-container').find('input').each(function(index, input){

            var value = $(input).val();
            var type = $(input).attr('type');
            var field = $(input).data('field');
            var jsonValue = JSON.stringify(fieldsJsonValue[field]);

            html += '<td class="field" data-field="' + field + '" data-value=' + "'" + jsonValue + "'" + '">';

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

        var table = $(btn).closest('.template-container-body').find('.image-blocks-table');
        initSortable(table);
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
