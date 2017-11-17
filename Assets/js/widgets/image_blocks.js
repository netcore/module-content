
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

        var imageBlockItemId = $(tr).data('image-block-item-id');
        var order = (index+1);
        var attributes = {};

        $(tr).find('td.field').each(function(i, td){

            var attribute = $(td).data('field');
            var value = $(td).data('value');

            attributes[attribute] = value;
        });

        blocks.push({
            'imageBlockItemId': imageBlockItemId,
            'order': order,
            'attributes': attributes
        });
    });

    return {
        'imageBlockId': $(widgetTr).find('.image-blocks-table').data('image-block-id'),
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

        var addNewcontainer = $(btn).closest('.add-new-container');

        // Revert "Add" button
        $(addNewcontainer).find('.add-new-image-block-button').show();

        // Empty input fields
        $(addNewcontainer).find('.add-new-image-block-table input').each(function(index, input){
            $(input).val(null);
        });

        // Empty wysiwyg
        $(addNewcontainer).find('.add-new-image-block-table textarea.image-blocks-summernote').each(function(index, textarea){
            if($(textarea).hasClass('initialized')) {
                $(textarea).summernote('code', '');
            } else {
                $(textarea).val('');
            }
        });

        // Remove errors
        $(btn).closest('.add-new-container').find('.has-error').removeClass('has-error');
        $(btn).closest('.add-new-container').find('.error-span').text('');

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

    var initSummernote = function(btn){

        // Initialize
        var widgetTr = $(btn).closest('.widget-tr');
        var textareas = $(widgetTr).find('.image-blocks-summernote:not(.initialized)');
        $.each(textareas, function(i, object){
            $(object).addClass('initialized').summernote({
                height: 100,
                width: 800,
                focus: true,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['style', ['style']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['insert', ['picture', 'link']]
                ],
                fontSizes: ['10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24']
            });
        });
    };

    // Init sortable on page load
    $('.image-blocks-table').each(function(index, table){
        initSortable(table);
    });

    var initImageTooltips = function(){

        $('.image-blocks-tr .field.has-image img').each(function(index, img){

            var title = $(img).attr('title');
            var naturalWidth = $(img)[0].naturalWidth;
            var naturalHeight = $(img)[0].naturalHeight;

            title += ' (' + naturalWidth + 'x' + naturalHeight + 'px)';

            $(img).attr('title', title);
        });

        $('[data-toggle=tooltip]').tooltip({}); // Bootstrap tooltip
    };

    initImageTooltips();

    var getFieldsJsonValue = function(btn, modelId){

        var translatableFields = [];
        var regularFields = [];
        $(btn).closest('.add-new-container').find('input[data-field], textarea[data-field], select[data-field]').each(function(index, input){

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

            var inputCheckboxTextarea = 'input[data-field="' + field + '"], textarea[data-field="' + field + '"], select[data-field="' + field + '"]';
            $(btn).closest('.add-new-container').find(inputCheckboxTextarea).each(function(i, input){

                var locale = $(input).data('locale');
                var field = $(input).data('field');
                var value = $(input).val();

                if($(input).is('input[type=checkbox]')) {
                    value = $(input).is(':checked');
                }

                jsonValue[locale] = value;
            });

            fieldsJsonValue[field] = jsonValue;
        });

        $(regularFields).each(function(i, field){

            var jsonValue = {};

            var inputCheckboxTextarea = 'input[data-field="' + field + '"], textarea[data-field="' + field + '"], select[data-field="' + field + '"]';
            $(btn).closest('.add-new-container').find(inputCheckboxTextarea).each(function(i, input){

                var type = $(input).attr('type');
                var value = $(input).val();

                if(type == 'file') {

                    var addNewContainer = $(btn).closest('.add-new-container');
                    var imageBlockUpdateId = $(addNewContainer).data('update-image-block-item-id');
                    if(imageBlockUpdateId && !value) {
                        return true; // Continue
                    }

                    var imageBlockId = imageBlockUpdateId ? imageBlockUpdateId : modelId;

                    var contentBlockId = $(btn).closest('.widget-tr').data('content-block-id');
                    var imageName = 'image-' + contentBlockId + '-' + imageBlockId + '-' + field; // Used to retrieve image in backend

                    //Append file (we use loop, but since this is not multiple, then there is only one image)
                    $.each($(input)[0].files, function(i, file) {
                        formDataImages[imageName] = file;
                    });

                    jsonValue['file'] = imageName
                } else {
                    jsonValue['value'] = value;
                }
            });

            fieldsJsonValue[field] = jsonValue;
        });

        return fieldsJsonValue;
    };

    var addNewRow = function(btn){

        var modelId = randomString();
        var containerBody = $(btn).closest('.template-container-body');
        var addNewContainer = $(btn).closest('.add-new-container');
        var updateId = $(addNewContainer).data('update-image-block-item-id');

        if(updateId) {
            modelId = updateId;
        }

        // Add new row
        var html = '<tr class="fade-out-' + modelId + ' image-blocks-tr" data-image-block-item-id="' + modelId + '">';

        var actionsTemplate = $('#image-block-actions-template').html();
        actionsTemplate = replaceAll('modelId', modelId, actionsTemplate);

        // Handler for dragndrop
        html += '<td class="cursor-dragndrop image-blocks-handle text-align-center vertical-align-middle">';
        html += '<span class="fa fa-icon fa-arrows"></span>';
        html += '</td>';

        // For each input - one td
        var fieldsJsonValue = getFieldsJsonValue(btn, modelId);

        $(addNewContainer).find('input[data-field], textarea[data-field], select[data-field]').each(function(index, input){

            var value = $(input).val();
            if($(input).is('input[type=checkbox]')) {
                value = $(input).is(':checked') ? 'Yes' : 'No';
            }

            if($(input).is('select')) {
                value = $(input).find('option:selected').text();
            }

            var type = $(input).attr('type');

            if( $(input).is('textarea') ) {
                type = 'textarea';
            }

            if( $(input).is('select') ) {
                type = 'select';
            }

            var field = $(input).data('field');
            var trIndex = $(btn).closest('.template-container-body').find('.image-blocks-tr').length;

            if(updateId) {
                trIndex--; // Index is zero based.
            }

            var tdId = trIndex + '-' + field;
            var jsonValue = JSON.stringify(fieldsJsonValue[field]);

            html += '<td class="field ' + (type==='file' ? 'has-image' : '') + '" data-field="' + field + '" data-value=' + "'" + jsonValue + "'" + '" data-td-id="' + tdId + '">';

            if( $.inArray(type, ['text', 'number', 'textarea', 'checkbox', 'select']) !== -1 ) {
                html += value;
            } else if( type === 'file' && value) {
                var src = URL.createObjectURL( input.files[0] );
                var imageWidth = $(input).data('image-width') || 50;
                html += '<img class="img-responsive" data-initialized="0" data-toggle="tooltip" data-placement="right" title="Test" data-container="body" style="width:' + imageWidth + 'px;" src="' + src + '">';
            }
            else if( type === 'file' && !value && updateId ) { // UPDATE, with image intact

                var existingImage = $(containerBody)
                    .find('.image-blocks-table tbody tr[data-image-block-item-id="' + updateId + '"]')
                    .find('td[data-field="' + field + '"]')
                    .html();

                html += existingImage;
            }
            else if( type === 'file' && !value && !updateId ) { // INSERT, with image not required

                html += 'No image';
            }

            html += '</td>';
        });

        // Actions
        html += '<td class="text-align-center vertical-align-middle">';
        html += actionsTemplate;
        html += '</td>';

        html += '</tr>';

        if(updateId) {

            // Find that row. And replace html.

            $(containerBody)
                .find('.image-blocks-table tbody tr[data-image-block-item-id="' + updateId + '"]')
                .replaceWith(html);

            $(addNewContainer).data('update-image-block-item-id', null);

        } else {

            $(containerBody).find('.image-blocks-table .no-blocks-tr').remove();
            var countOfTrsInBody = $(containerBody).find('.image-blocks-table tbody tr').length;

            if(countOfTrsInBody) {
                $(containerBody).find('.image-blocks-table tbody tr:last').after(html);
            } else {
                $(containerBody).find('.image-blocks-table tbody').html(html);
            }
        }


        var table = $(btn).closest('.template-container-body').find('.image-blocks-table');

        $(table).find('[data-toggle="tooltip"][data-initialized=0]').each(function(i, img){

            if($(img).data('initialized')) {
                return true;
            }

            img.onload = function(){

                var title = '';
                var naturalWidth = $(img)[0].naturalWidth;
                var naturalHeight = $(img)[0].naturalHeight;

                title += ' (' + naturalWidth + 'x' + naturalHeight + 'px)';

                $(img).attr('title', title);

                $(img).tooltip();
                $(img).data('initialized', 1);
            };
        });

        initSortable(table);
    };

    $('body').on('click', '.edit-image-block', function(){

        // Show form
        var templateContainer = $(this).closest('.template-container-body');
        var addNewContainer = $(templateContainer).find('.add-new-container');

        var imageBlockItemId = $(this).closest('.image-blocks-tr').data('image-block-item-id');
        $(addNewContainer).data('update-image-block-item-id', imageBlockItemId);

        $(templateContainer).find('.add-new-image-block-button').hide();
        $(templateContainer).find('.add-new-image-block-table').fadeIn();

        // Load all data, except image
        $(this).closest('.image-blocks-tr').find('td.field').each(function(i, td){
            var field = $(td).data('field');
            var json = $(td).data('value');

            $.each(json, function(isoCode, value){

                var element = $(addNewContainer).find('[data-field="' + field + '"][data-locale="' + isoCode + '"]');

                if($(element).hasClass('image-blocks-summernote') && $(element).hasClass('initialized')) {
                    $(element).summernote('code', value);
                } else {
                    $(element).val(value);
                }
            });

        });

        initSummernote(this);
    });

    $('body').on('click', '.add-new-image-block-button', function(){
        $(this).hide();
        $(this).closest('.add-new-container').find('.add-new-image-block-table').fadeIn();

        initSummernote(this);
    });

    $('body').on('click', '.add-new-image-block-cancel', function(){
        clearAddNewImageBlockForm(this);
    });

    $('body').on('click', '.add-new-image-block-submit', function(){

        var valid = true;

        var addNewContainer = $(this).closest('.add-new-container');

        // Remove previous errors
        $(addNewContainer).find('.has-error').removeClass('has-error');
        $(addNewContainer).find('.error-span').text('');

        // Input
        $(addNewContainer).find('input[data-field], textarea[data-field]').each(function(index, input){

            var type = $(input).attr('type');
            var notRequired = $(input).data('not-required') || false;
            var isUpdate = $(this).closest('.add-new-container').data('update-image-block-item-id');

            if(type == 'file' && isUpdate) {
                return true; // continue
            }

            if(notRequired) {
                return true;
            }

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

