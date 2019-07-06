// scripts for meta description settings page
$(function () {
    $("#meta_description_settings")
            .ajaxForm(
                    {
                        beforeSubmit: function () {
                            $("#message").html("");
                            $("#loading").show();
                        },
                        success: function () {
                            $("#loading").hide();
                            $("#message")
                                    .html(
                                            "<span style=\"color:green;\">Die Einstellungen wurden gespeichert.</span>");
                        }
                    });
});
