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

function showMergeModal(event){
	event.preventDefault();
	
	var optionElements = $("#checkout_branch_form select[name='name'] option");
	var selectedOption = $("#checkout_branch_form select[name='name'] option");
	
	var options = [
	{
		text:"Select Branch",
		value: -1
	}
	];
	optionElements.each(function(index, element){
		options.push({
			text: element.text,
			value: element.value
					});
	});

	bootbox.prompt({
			title: "Merge Branch", 
			inputType: "select",
			inputOptions: options,
			value: -1,
			callback: function(result) {
				if(result!== null && result != -1){
					alert("branch selected" + result);
					// Do Ajax
					// If there is an error show it in a bootbox.alert
					// If merge was successfull reload the page
				}
			}
		});
}