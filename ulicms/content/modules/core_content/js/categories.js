// This the script for the categories list page
$(() => {
    $("form.delete-form").ajaxForm({
        success: (responseText, statusText, xhr, $form) => {
            const action = $($form).attr("action");
            const params = new URLSearchParams(action);
            const id = params.get("del");

            const list_item_id = `dataset-${id}`;
            const tr = $(`tr#${list_item_id}`);

            $(tr).fadeOut();
        }
    });
});