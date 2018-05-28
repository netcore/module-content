
/**
 *
 *  Base64 encode / decode
 *  http://www.webtoolkit.info
 *
 **/
var Base64 = {

    // private property
    _keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/="

    // public method for encoding
    , encode: function (input)
    {
        var output = "";
        var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
        var i = 0;

        input = Base64._utf8_encode(input);

        while (i < input.length)
        {
            chr1 = input.charCodeAt(i++);
            chr2 = input.charCodeAt(i++);
            chr3 = input.charCodeAt(i++);

            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;

            if (isNaN(chr2))
            {
                enc3 = enc4 = 64;
            }
            else if (isNaN(chr3))
            {
                enc4 = 64;
            }

            output = output +
                this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
                this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);
        } // Whend

        return output;
    } // End Function encode


    // public method for decoding
    ,decode: function (input)
    {
        var output = "";
        var chr1, chr2, chr3;
        var enc1, enc2, enc3, enc4;
        var i = 0;

        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
        while (i < input.length)
        {
            enc1 = this._keyStr.indexOf(input.charAt(i++));
            enc2 = this._keyStr.indexOf(input.charAt(i++));
            enc3 = this._keyStr.indexOf(input.charAt(i++));
            enc4 = this._keyStr.indexOf(input.charAt(i++));

            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;

            output = output + String.fromCharCode(chr1);

            if (enc3 != 64)
            {
                output = output + String.fromCharCode(chr2);
            }

            if (enc4 != 64)
            {
                output = output + String.fromCharCode(chr3);
            }

        } // Whend

        output = Base64._utf8_decode(output);

        return output;
    } // End Function decode


    // private method for UTF-8 encoding
    ,_utf8_encode: function (string)
    {
        var utftext = "";
        string = string.replace(/\r\n/g, "\n");

        for (var n = 0; n < string.length; n++)
        {
            var c = string.charCodeAt(n);

            if (c < 128)
            {
                utftext += String.fromCharCode(c);
            }
            else if ((c > 127) && (c < 2048))
            {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            }
            else
            {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }

        } // Next n

        return utftext;
    } // End Function _utf8_encode

    // private method for UTF-8 decoding
    ,_utf8_decode: function (utftext)
    {
        var string = "";
        var i = 0;
        var c, c1, c2, c3;
        c = c1 = c2 = 0;

        while (i < utftext.length)
        {
            c = utftext.charCodeAt(i);

            if (c < 128)
            {
                string += String.fromCharCode(c);
                i++;
            }
            else if ((c > 191) && (c < 224))
            {
                c2 = utftext.charCodeAt(i + 1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            }
            else
            {
                c2 = utftext.charCodeAt(i + 1);
                c3 = utftext.charCodeAt(i + 2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }

        } // Whend

        return string;
    } // End Function _utf8_decode

}

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
onWidgetAdded['widget_blocks'] = function(widgetTr) {
};

/**
 *
 * Function that you put into widgetDataCollectors object
 * will be called to collect data from widget for usage in backend.
 *
 * widgetTr is <tr></tr> element that houses widget
 *
 */
widgetDataCollectors['widget_blocks'] = function(widgetTr) {

    var blocks = [];

    $(widgetTr).find('.image-blocks-tr').each(function(index, tr){

        var widgetBlockItemId = $(tr).data('image-block-item-id');
        var order = (index+1);
        var attributes = {};

        $(tr).find('td.field').each(function(i, td){

            var attribute = $(td).data('field');
            var value = JSON.parse(
                Base64.decode(
                    $(td).data('value')
                )
            );

            attributes[attribute] = value;
        });

        blocks.push({
            'widgetBlockItemId': widgetBlockItemId,
            'order': order,
            'attributes': attributes
        });
    });

    return {
        'widgetBlockId': $(widgetTr).find('.image-blocks-table').data('image-block-id'),
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

    var clearAddNewWidgetBlockForm = function(btn){

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
                height: 210,
                focus: true,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['style', ['style']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['insert', ['picture', 'video', 'link', 'table']],
                    ['codeview', ['codeview']],
                    ['cleaner',['cleaner']]
                ],
                cleaner:{
                    action: 'both', // both|button|paste 'button' only cleans via toolbar button, 'paste' only clean when pasting content, both does both options.
                    newline: '<br>', // Summernote's default is to use '<p><br></p>'
                    notStyle: 'position:absolute;top:0;left:0;right:0', // Position of Notification
                    icon: '<i class="note-icon"><span class="fa fa-paint-brush"></span></i>',
                    keepHtml: false, // Remove all Html formats
                    keepOnlyTags: ['<p>', '<br>', '<ul>', '<li>', '<b>', '<strong>','<i>', '<a>'], // If keepHtml is true, remove all tags except these
                    keepClasses: false, // Remove Classes
                    badTags: ['style', 'script', 'applet', 'embed', 'noframes', 'noscript', 'html'], // Remove full tags with contents
                    badAttributes: ['style', 'start'], // Remove attributes from remaining tags
                    limitChars: false, // 0/false|# 0/false disables option
                    limitDisplay: 'both', // text|html|both
                    limitStop: false // true/false
                },
                fontSizes: ['10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24'],
                tableClassName: 'table ntc-table'
            });
        });
    };

    // Init sortable on page load
    $('.image-blocks-table').each(function(index, table){
        initSortable(table);
    });

    $('.js-main-fields-block').each(function(index, block){
        initSummernote(block);
    });

    var initMainWidgetFields = function () {
        $('.widget-tr').each(function(index, widget){
            var id = $(widget).data('content-block-id');
            $(widget).find('.js-main-fields-block .js-input').each(function (i, field) {
                $(field).attr('name', 'main_fields[' + id + '][' + $(field).data('name') + ']');
            });
        });
    };

    initMainWidgetFields();

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
                if($(input).is('input[type=checkbox]')) {
                    value = $(input).is(':checked');
                }

                if(type == 'file') {

                    var addNewContainer = $(btn).closest('.add-new-container');
                    var widgetBlockUpdateId = $(addNewContainer).data('update-image-block-item-id');
                    if(widgetBlockUpdateId && !value) {
                        return true; // Continue
                    }

                    var widgetBlockId = widgetBlockUpdateId ? widgetBlockUpdateId : modelId;

                    var contentBlockId = $(btn).closest('.widget-tr').data('content-block-id');
                    var imageName = 'image-' + contentBlockId + '-' + widgetBlockId + '-' + field; // Used to retrieve image in backend

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
            var jsonValue = Base64.encode(JSON.stringify(fieldsJsonValue[field]));

            html += '<td class="field ' + (type==='file' ? 'has-image' : '') + '" data-field="' + field + '" data-value="' + jsonValue + '" data-td-id="' + tdId + '">';

            if( $.inArray(type, ['text', 'number', 'textarea', 'checkbox', 'select']) !== -1 ) {
                html += value;
            } else if( type === 'file' && value) {
                $.each(input.files, function (i, file) {
                    var src = URL.createObjectURL( file );
                    var imageWidth = $(input).data('image-width') || 75;
                    html += '<img class="img-responsive" data-initialized="0" data-toggle="tooltip" data-placement="right" title="Test" data-container="body" style="width:' + imageWidth + 'px;" src="' + src + '"> <br>';
                });

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
        dontAllowMoreItemsThanMaxCount();
    };

    $('body').on('click', '.edit-image-block', function(){

        // Show form
        var templateContainer = $(this).closest('.template-container-body');
        var addNewContainer = $(templateContainer).find('.add-new-container');

        var widgetBlockItemId = $(this).closest('.image-blocks-tr').data('image-block-item-id');
        $(addNewContainer).data('update-image-block-item-id', widgetBlockItemId);

        $(templateContainer).find('.add-new-image-block-button').hide();
        $(templateContainer).find('.add-new-image-block-table').fadeIn();

        // Load all data, except image
        $(this).closest('.image-blocks-tr').find('td.field').each(function(i, td){
            var field = $(td).data('field');
            var json = JSON.parse(
                Base64.decode(
                    $(td).data('value')
                )
            );

            $.each(json, function(isoCode, value){

                var element = $(addNewContainer).find('[data-field="' + field + '"][data-locale="' + isoCode + '"]');

                if($(element).hasClass('image-blocks-summernote') && $(element).hasClass('initialized')) {
                    $(element).summernote('code', value);
                }
                else if($(element).is(':checkbox')){
                    var bool = value == true;
                    $(element).prop('checked', bool);
                }
                else {
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
        clearAddNewWidgetBlockForm(this);
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

            if($(input).is('input[type=checkbox]')) {
                return true;
            }

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
        clearAddNewWidgetBlockForm(this);
    });

    var dontAllowMoreItemsThanMaxCount = function(){
        $('.image-blocks-table').each(function(index, table){

            // get maximum
            var templateContainer = $(table).closest('.template-container-body');
            var itemsMaxCount = $(templateContainer).find('.add-new-container').data('max-items-count');

            // get current
            var itemsCurrentCount = $(table).find('tbody .image-blocks-tr').length;

            // disable/enable accordingly
            var disabled = itemsMaxCount && (itemsCurrentCount >= itemsMaxCount);

            var button = $(templateContainer).find('.add-new-image-block-button');
            if(disabled) {
                $(button).addClass('disabled');
            } else {
                $(button).removeClass('disabled');
            }
        });
    };

    $('body').on('click', '.swal2-container', function(){
        window.setTimeout(function(){
            dontAllowMoreItemsThanMaxCount();
        }, 500);
    });

    // Initial
    dontAllowMoreItemsThanMaxCount();
})();

