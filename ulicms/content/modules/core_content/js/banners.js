// This the script for the advertisement banners list page
$(function () {
    // when select another language
    $('#category_id').on(
            'change',
            function () {
                var valueSelected = $('#category_id').val();
                location.replace("index.php?action=banner&filter_category="
                        + valueSelected);
            });
    // delete button
    $("form.delete-form").ajaxForm({
        success: function (responseText, statusText, xhr, $form) {
            var action = $($form).attr("action");

            var params = new URLSearchParams(action);

            var id = params.get("banner");
            var list_item_id = `dataset-${id}`;
            var tr = $(`tr#${list_item_id}`);

            $(tr).fadeOut();
        }
    });
});
