$(function() {

    // Slug generation
    $('body').on('keyup', 'input.name', function(){
        var title = $(this).val();
        console.log(title);
        var slug = Netcore.slugify(title);

        $(this).closest('form').find('input.slug').val(slug);
        //$(this).closest('.tab-pane').find('input.slug').val(slug);
    });
});
