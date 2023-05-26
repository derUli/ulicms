/* global Translation */

$(() => {
    $("select#language").change(() => {
        const url = "index.php?action=motd&language="
                + $("select#language option:selected").val();
        location.replace(url);
    });

    $("#motd_form").ajaxForm(
            {
                beforeSubmit: () => {
                    $("#loading").show();
                },
                // FIXME: this is copy and paste code
                // move this to a util method
                beforeSerialize: () => {
                    /* Before serialize */
                    updateCKEditors();
                    return true;
                },
                success: () => {
                    $("#loading").hide();
                    vanillaToast.success(Translation.ChangesWereSaved);
                }
            });
});