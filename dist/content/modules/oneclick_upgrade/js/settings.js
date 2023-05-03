
const updateChannelHelp = () => {
    $("#help-texts > div").hide();
    const selected = $("#oneclick_upgrade_channel").val();
    $(`#help-texts > div[data-channel='${selected}']`).show();
};


$(() => {
    updateChannelHelp();
    $("#oneclick_upgrade_channel").change(updateChannelHelp);

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
