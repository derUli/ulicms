// This the script for the categories list page
$(() => {
    $("form.delete-form").ajaxForm({
        success: (responseText, statusText, xhr, $form) => {
            const action = $($form).attr("action");
            const params = new URLSearchParams(action);
            const id = params.get("del");

            const list_item_id = `dataset-${id}`;
            const row = $(`tr#${list_item_id}`);

            const table = row.closest('table');
            const dataTable = table.DataTable();

            $(row).fadeOut(400, () => {
                dataTable.row(row).remove().draw(false);
            });
            
        }
    });
});