jQuery(function ($) {
    'use strict';

    function idsFromPreview() {
        var ids = [];
        $('#tpg-gallery-preview .tpg-gallery-thumb').each(function () {
            ids.push($(this).data('id'));
        });
        $('#tpg_gallery_ids').val(ids.join(','));
    }

    $('#tpg-gallery-preview').sortable({
        handle: '.tpg-sort-handle',
        update: idsFromPreview
    });

    $(document).on('click', '#tpg-select-gallery', function (e) {
        e.preventDefault();

        var frame = wp.media({
            title: 'Select Portfolio Gallery Images',
            button: { text: 'Use selected images' },
            multiple: true,
            library: { type: 'image' }
        });

        frame.on('select', function () {
            var selection = frame.state().get('selection');
            selection.each(function (attachment) {
                var data = attachment.toJSON();
                if ($('#tpg-gallery-preview .tpg-gallery-thumb[data-id="' + data.id + '"]').length) {
                    return;
                }

                var thumb = data.sizes && data.sizes.thumbnail ? data.sizes.thumbnail.url : data.url;
                var html =
                    '<div class="tpg-gallery-thumb" data-id="' + data.id + '">' +
                        '<img src="' + thumb + '" alt="">' +
                        '<button type="button" class="tpg-remove-gallery-image" aria-label="Remove image">&times;</button>' +
                        '<span class="dashicons dashicons-move tpg-sort-handle"></span>' +
                    '</div>';

                $('#tpg-gallery-preview').append(html);
            });
            idsFromPreview();
        });

        frame.open();
    });

    $(document).on('click', '.tpg-remove-gallery-image', function () {
        $(this).closest('.tpg-gallery-thumb').remove();
        idsFromPreview();
    });

    function singleImagePicker(buttonSelector, inputSelector, previewSelector) {
        $(document).on('click', buttonSelector, function (e) {
            e.preventDefault();

            var frame = wp.media({
                title: 'Choose Banner Image',
                button: { text: 'Use this image' },
                multiple: false,
                library: { type: 'image' }
            });

            frame.on('select', function () {
                var item = frame.state().get('selection').first().toJSON();
                var preview = item.sizes && item.sizes.medium ? item.sizes.medium.url : item.url;
                $(inputSelector).val(item.id);
                $(previewSelector).html('<img src="' + preview + '" alt="">');
            });

            frame.open();
        });
    }

    singleImagePicker('#tpg-select-banner', '#tpg_banner_id', '#tpg-banner-preview');
    singleImagePicker('#tpg-select-global-banner', '#tpg_global_banner_id', '#tpg-global-banner-preview');

    $(document).on('click', '#tpg-remove-banner', function (e) {
        e.preventDefault();
        $('#tpg_banner_id').val('');
        $('#tpg-banner-preview').empty();
    });

    $(document).on('click', '#tpg-remove-global-banner', function (e) {
        e.preventDefault();
        $('#tpg_global_banner_id').val('');
        $('#tpg-global-banner-preview').empty();
    });

    function updateFrontendFilterOrder() {
        var order = [];

        $('#tpg-selected-filters .tpg-filter-sort-item').each(function () {
            order.push(String($(this).data('filter-key')));
        });

        $('#tpg_filter_order').val(order.join(','));
    }

    $('.tpg-filter-sortable').sortable({
        connectWith: '.tpg-filter-sortable',
        handle: '.tpg-filter-drag',
        placeholder: 'tpg-filter-sort-placeholder',
        forcePlaceholderSize: true,
        update: updateFrontendFilterOrder,
        receive: updateFrontendFilterOrder
    });

    updateFrontendFilterOrder();

});
