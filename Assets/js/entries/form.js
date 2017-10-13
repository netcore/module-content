$(function() {

    var initSortable = function(){
        // Orderable shop images
        $('#widgets-table').sortable({
            containerSelector: 'table',
            itemPath : '> tbody',
            itemSelector : 'tr',
            handle : '.handle',
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

    var hideOrShowCountMessage = function(){
        var count = $('#widgets-table tr').length;
        if(!count) {
            $('#widgets-container #no-widgets').show();
        } else {
            $('#widgets-container #no-widgets').hide();
        }
    };

    var replaceAll = function(search, replacement, source) {
        return source.split(search).join(replacement);
    };

    $.get('/admin/content/entries/widgets', function(widgets){

        $('body').on('click', '#add-widget-button', function(){
            var key = $('#select-widget option:selected').val();
            var data = widgets[key];

            var id = '';
            var template = data.backend_template || data.name;
            
            var withBorder = data.backend_with_border ? 'with-border' : '';

            var html = $('#widget-tr-template').html();
            html = replaceAll('{{ id }}', id, html);
            html = replaceAll('{{ key }}', key, html);
            html = replaceAll('{{ withBorder }}', withBorder, html);
            html = replaceAll('{{ template }}', template, html);

            if( $('#widgets-table tbody tr').length ) {
                $('#widgets-table tbody tr:last').after(html);
            } else {
                $('#widgets-table tbody').html(html);
            }

            hideOrShowCountMessage();
            initSortable();

            var callable = onWidgetAdded[key];
            if(callable){
                var widgetTr = $('#widgets-table tr:last');
                callable(widgetTr);
            }
        });
    });

    // Delete widget blocks
    $('body').on('click', '.delete-widget', function(){

        var closestTr = $(this).closest('tr');

        swal({
            title: 'Are you sure?',
            text: 'Do you really want to remove this block?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            confirmButtonColor: "#DD6B55",
            cancelButtonText: 'Cancel'
        }).then(function() {
            // Yes
            $(closestTr).fadeOut(function(){
                console.log('Callback');
                $(this).remove();
            });
        }, function(dismiss) {
            // Cancel
        });
    });

    // On page load, initialize widgets
    $('#widgets-table tr').each(function(index, widgetTr){

        var key = $(widgetTr).data('key');

        var callable = onWidgetAdded[key];
        if(callable){
            callable(widgetTr);
        }
    });

    // After widgets have been initialised - init sortable
    initSortable();

    // Init switchery
    $('.switchery').each(function(i, switcher) {
        new Switchery(switcher);
        $(switcher).closest('.hidden-switchery').show();
    });

    $('body').on('click', '#submit-button', function(){

        var dataForBackend = $(this).closest('form').serializeArray();

        var widgets = [];

        $('#widgets-table tr').each(function(i, o){

            var key = $(o).data('key');
            var collector = widgetDataCollectors[key];

            var item = {
                order: i,
                widget: key
            };

            if(collector) {
                item['data'] = collector($(this));
            }

            widgets.push(item);
        });

        dataForBackend.push({
            name: 'widgets',
            value: JSON.stringify(widgets)
        });

        var form = $(this).closest('form');

        // Reset errors
        $(form).find('.form-group.has-error').removeClass('has-error');
        $(form).find('.error-span').text('');

        // Post to backend
        $.ajax({
            url: $(form).attr('action'),
            type: $(form).attr('method'),
            data: dataForBackend,
            dataType: 'json',
            success: function (response) {

                $.growl.notice({
                    title : 'Success!',
                    message : 'Data saved!'
                });

                if(response.redirect_to) {
                    window.location.href = response.redirect_to;
                }
            },
            error: function (xhr) {
                var errors = xhr.responseJSON.errors;

                $.each(errors, function(key, value){

                    if(key == 'widgets') {

                    } else {
                        var splitted = key.split('.');

                        var htmlName = splitted[0];
                        splitted.shift();

                        $.each(splitted, function(i, string){
                            htmlName += '[' + string + ']';
                        });

                        var formGroup = $('input[name="' + htmlName + '"]').closest('.form-group');
                        $(formGroup).addClass('has-error');
                        $(formGroup).find('.error-span').text(value);
                    }
                });
            }
        });
    });
});
