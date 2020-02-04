// Script for the "privacy settings" page
$(() => {
	$("form#privacy_form")
        .ajaxForm(
                {
                    beforeSubmit: () => {
                        $("#message").html("");
                        $("#loading").show();
                    },
                    success: () => {
                        $("#loading").hide();
                        $("#message")
                                .html(`<span style="color:green;">
                        ${Translation.ChangesWasSaved}
                </span>`);
                    }
                });
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
});