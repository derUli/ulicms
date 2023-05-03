// Script for the "privacy settings" page
$(() => {
    // Change language
    // Privacy settings are language specific
    $("select#language").change(
            () => {
        const url = "index.php?action=privacy_settings&language="
                + $("select#language option:selected").val();
        location.replace(url);
    });

    // expand privacy policy checkbox options when enabled
    $("#privacy_policy_checkbox_enable").change((event) => {
        const checked = $(event.currentTarget).is(":checked");

        if (checked) {
            $("#privacy_policy_checkbox_text_container").slideDown();
            // CodeMirror is not correctly initialized if initially hidden. Reinitialize the CodeMirror Editor after toggling the editor
            refreshCodeMirrors();
        } else {
            $("#privacy_policy_checkbox_text_container").slideUp();
        }
    });

    $('#delete_ips_after_48_hours').change((event) => {
        const checked = $(event.currentTarget).is(":checked");

        if (checked) {
            $("#privacy_policy_keep_spam_ips_container").slideDown();
            // CodeMirror is not correctly initialized if initially hidden. Reinitialize the CodeMirror Editor after toggling the editor
            refreshCodeMirrors();
        } else {
            $("#privacy_policy_keep_spam_ips_container").slideUp();
        }
    });


    $("#privacy-form").ajaxForm(
        {
            beforeSubmit: () => {
                $("#message").html("");
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