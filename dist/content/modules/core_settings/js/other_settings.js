/* global Translation */

// scripts for meta description settings page
$(() => {
    const showSections = () => {
        if ($('#email_mode').val() === "phpmailer") {
            $('#smtp_settings').slideDown();
        } else {
            $('#smtp_settings').slideUp();
        }
        if ($('#smtp_auth').is(':checked')) {
            $('#smtp_auth_div').slideDown();
        } else {
            $('#smtp_auth_div').slideUp();
        }
    };

    showSections()
    $('#email_mode, #smtp_auth').change(showSections);

    $("#other_settings").ajaxForm(
            {
                beforeSubmit: () => {
                    $("#loading").show();
                },
                success: () => {
                    $("#loading").hide();
                    vanillaToast.success(Translation.ChangesWereSaved);
                }
            });
});
