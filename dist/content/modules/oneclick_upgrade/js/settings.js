
const updateChannelHelp = () => {
    $("#help-texts > div").hide();
    const selected = $("#oneclick_upgrade_channel").val();
    $(`#help-texts > div[data-channel='${selected}']`).show();
};

updateChannelHelp();

$("#oneclick_upgrade_channel").change(updateChannelHelp);