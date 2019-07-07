$(function () {
    $.post("index.php", {
        ajax_cmd: "anyUpdateAvailable"
    }, function (data) {
        if (data === "yes") {
            $("#update-manager-dashboard-container").slideDown();
        }
    });
});