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
                    $("#message").html("");
                    $("#loading").show();
                },
                success: () => {
                    $("#loading").hide();
                    $("#message").html(
                            "<span style=\"color:green;\">"
                            + Translation.ChangesWasSaved + "</span>");
                    $("#loading").hide();
                }
            });
});