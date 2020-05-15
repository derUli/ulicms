/* global Translation */

// scripts for meta description settings page
$(() => {
    $("#footer_text_form").ajaxForm(
            {
                beforeSubmit: () => {
                    $("#message").html("");
                    $("#loading").show();
                },
                beforeSerialize: () => {
                    /* Before serialize */
                    for (instance in CKEDITOR.instances) {
                        CKEDITOR.instances[instance].updateElement();
                    }
                    return true;
                },
                success: () => {
                    $("#loading").hide();
                    $("#message")
                            .html(`<span style="color:green;">${Translation.ChangesWasSaved}</span>`);
                }
            });
});
