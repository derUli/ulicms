/* global Translation */

// scripts for meta description settings page
$(() => {
    $("#footer_text_form").ajaxForm(
            {
                beforeSubmit: () => {
                    $("#loading").show();
                },
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
