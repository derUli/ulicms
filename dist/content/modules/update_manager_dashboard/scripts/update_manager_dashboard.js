$(() => {
    $.post("index.php", {
        ajax_cmd: "anyUpdateAvailable"
    }, (data) => {
        if (data === "yes") {
            $("#update-manager-dashboard-container").slideDown();
        }
    });
});