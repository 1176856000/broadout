require([
    'jquery',
], function ($) {
    $('[name="entity"]').change(function (item) {
        var fieldSelector = '#upload_file_fieldset .field-import_images_file_dir',
            message = $.mage.__('Please, upload all the images into pub/media/amasty/review folder.' +
                'Note: the image path must be the same as the path specified in CSV file');
        if (item.currentTarget.value === 'import_reviews') {
            $(fieldSelector).hide();
            $('<div class="import-image-message">' + message + '</div>').insertAfter('.field-import_file');
        } else {
            $(fieldSelector).show();
            $('.import-image-message').remove();
        }
    })
});