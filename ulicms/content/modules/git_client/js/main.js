GIT_CHECK_INTERVAL = 10 * 1000; // Any 10 Seconds

$(function () {
    setTimeout(function () {
        checkForChanges();
    }, GIT_CHECK_INTERVAL);
});


// This method polls in a regular interval for changes
// and reloads the page if there are any
function checkForChanges() {
    var alertMessage = $("#alert-changes");
    var url = alertMessage.data("url");
    var hasChanges = alertMessage.data("has-changes");
    var branch = $("#checkout_branch_form select").val();
    $.ajax({
        type: 'GET',
        url: url,
        success: function (result) {
            if (hasChanges !== result["hasChanges"] || result["branch"] !== branch) {
                location.replace(location.href);
                return;
            }
            setTimeout(function () {
                checkForChanges();
            }, GIT_CHECK_INTERVAL);
        }
    });
}