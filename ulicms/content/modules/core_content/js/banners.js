// This the script for the advertisement banners list page
$(() => {
    // when select another language
    $('#category_id').on(
            'change',
            () => {
        const valueSelected = $('#category_id').val();
        location.replace("index.php?action=banner&filter_category="
                + valueSelected);
    });
    // delete button
    $("form.delete-form").ajaxForm({
        success: (responseText, statusText, xhr, $form) => {
            const action = $($form).attr("action");

            const params = new URLSearchParams(action);
            const id = params.get("banner");
            const list_item_id = `dataset-${id}`;
            const tr = $(`tr#${list_item_id}`);

            $(tr).fadeOut();
        }
    });
});
