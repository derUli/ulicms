$(() => {
    $(".ajax-form").ajaxForm(
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
