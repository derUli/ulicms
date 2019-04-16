$(function () {
    $("#spamfilter_settings").ajaxForm({beforeSubmit: function (e) {
            $("#message").html("");
            $("#loading").show();
        },
        success: function (e) {
            $("#loading").hide();
            $("#message").html("<span style=\"color:green;\">" + SettingsTranslation.ChangesWasSaved + "</span>");
        }
    });

    $("#spamfilter_enabled").change(function (event) {
        if (event.target.checked) {
            $('#country_filter_settings').slideDown()

        } else
        {
            $('#country_filter_settings').slideUp()
        }
    });
});