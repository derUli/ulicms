// The script for the frontpage settings page
$(function () {
    $("#frontpage_settings")
            .ajaxForm(
                    {
                        beforeSubmit: function () {
                            $("#message").html("");
                            $("#loading").show();
                        },
                        success: function () {
                            $("#loading").hide();
                            $("#message")
                                    .html(`<span style="color:green;">${Translation.ChangesWasSaved}</span>`);
                        }
                    });
});