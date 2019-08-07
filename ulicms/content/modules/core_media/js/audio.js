// Script for list of audio media
$(() => {
    $('#category').on(
            'change',
            () => {
        const valueSelected = $('#category').val();
        location.replace("index.php?action=audio&filter_category="
                + valueSelected);

    });
    const ajaxOptions = {
        success: (responseText, statusText, xhr, $form) => {
            const action = $($form).attr("action");
            const params = new URLSearchParams(action);

            const id = params.get("delete");
            const list_item_id = `dataset-${id}`;
            const tr = $(`tr#${list_item_id}`);
            $(tr).fadeOut();
        }
    };

    $("form.delete-form").ajaxForm(ajaxOptions);
});