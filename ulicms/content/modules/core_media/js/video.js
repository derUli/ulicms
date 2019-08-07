// Script for list of video media
$(() => {
    $('#category').on(
            'change',
            () => {
        const valueSelected = $('#category').val();
        location.replace(`index.php?action=videos&filter_category=${valueSelected}`);
    });
    var ajaxOptions = {
        success: (responseText, statusText, xhr, $form) => {
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