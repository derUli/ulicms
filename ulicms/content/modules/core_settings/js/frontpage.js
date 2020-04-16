// The script for the frontpage settings page
$(() => {
    $("#frontpage_settings").ajaxForm(
            {
                beforeSubmit: () => {
                    $("#message").html("");
                    $("#loading").show();
                },
                success: () => {
                    $("#loading").hide();
                    $("#message").html(
                            `<span style="color:green;">
                            ${Translation.ChangesWasSaved}
                </span>`);
                }
            });
});