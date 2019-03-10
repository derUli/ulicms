GIT_CHECK_INTERVAL = 10 * 1000 // Any 10 Seconds

$(function () {
    setTimeout(function () {
        checkForChanges();
    }, GIT_CHECK_INTERVAL);

})

function checkForChanges() {
    var alertMessage = $("#alert-changes");
    var url = alertMessage.data("url");
    var hasChanges = alertMessage.data("has-changes");
    $.ajax({
        type: 'GET',
        url: url,
        success: function (result) {
            if (hasChanges !== result) {
                location.replace(location.href);
                return;
            }
            setTimeout(function () {
                checkForChanges();
            }, GIT_CHECK_INTERVAL);
        }
    });
}