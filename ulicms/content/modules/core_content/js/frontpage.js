// The script for the frontpage settings page
$(function () {
    $("#frontpage_settings")
            .ajaxForm(
                    {
                        beforeSubmit: function (e) {
                            $("#message").html("");
                            $("#loading").show();
                        },
                        success: function (e) {
                            $("#loading").hide();
                            $("#message")
                                    .html(
                                            "<span style=\"color:green;\">Die Einstellungen wurden gespeichert.</span>");
                        }
                    });
});