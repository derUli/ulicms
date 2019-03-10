GIT_CHECK_INTERVAL = 10 * 1000; // Any 10 Seconds

$(function () {
    setTimeout(function () {
        checkForChanges();
    }, GIT_CHECK_INTERVAL);
    $("#btn-merge").click(showMergeModal)
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

function showMergeModal(event) {
    var url = $(event.target).data("url");
    event.preventDefault();

    var optionElements = $("#checkout_branch_form select[name='name'] option");
    var selectedOption = $("#checkout_branch_form select[name='name'] option");

    var options = [
        {
            text: Translation.SelectBranch,
            value: -1
        }
    ];
    optionElements.each(function (index, element) {
        options.push({
            text: element.text,
            value: element.value
        });
    });

    bootbox.prompt({
        title: Translation.MergeBranch,
        buttons: {
            confirm: {
                label: '<i class="fas fa-check"></i> ' + Translation.GitMerge,
                className: 'btn-success'
            },
            cancel: {
                label: '<i class="fas fa-times"></i> ' + Translation.Cancel,
                className: 'btn-danger'
            }
        },
        inputType: "select",
        inputOptions: options,
        value: -1,

        callback: function (result) {
            if (result !== null && result != -1) {
                // Do Ajax
                // If there is an error show it in a bootbox.alert
                // If merge was successfull reload the page
                $.ajax({
                    type: 'GET',
                    url: url + "&name=" + encodeURI(result),
                    success: function (result) {
                        location.replace(location.href);
                    },
                    error: function (jqXhr, textStatus, errorThrown) {
                        bootbox.alert(
                                {
                                    title: errorThrown,
                                    message: jqXhr.responseText
                                }
                        );
                    }
                });
            }
        }
    });
}