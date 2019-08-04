// Script for list of audio media
$(function () {
    $('#category').on(
            'change',
            function (e) {
                var valueSelected = $('#category').val();
                location.replace("index.php?action=audio&filter_category="
                        + valueSelected);

            });
    var ajaxOptions = {
        success: function (responseText, statusText, xhr, $form) {
            var action = $($form).attr("action");
            var params = new URLSearchParams(action);

            var id = params.get("delete");
            var list_item_id = `dataset-${id}`;
            var tr = $(`tr#${list_item_id}`);
            $(tr).fadeOut();
        }
    };

    $("form.delete-form").ajaxForm(ajaxOptions);
});