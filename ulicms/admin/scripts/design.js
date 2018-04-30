function initMobileDetectNotice() {
	if ($("select[name='mobile_theme']").val() != ""
			&& $("#mobile_detect_notice").data("installed") == false) {
		$("#mobile_detect_notice").slideDown();
	} else {
		$("#mobile_detect_notice").slideUp();
	}
}

function onChangeDefaultFont() {
	var value = $("select#default-font").val();
	if (value == "google") {
		$("div#google-fonts").slideDown();
	} else {

		$("div#google-fonts").slideUp();
	}
}

$(function() {
	$("#mobile_detect_notice").hide();
	initMobileDetectNotice();
	$("select[name='mobile_theme']").change(function() {
		initMobileDetectNotice();
	});

	$("select#default-font").change(onChangeDefaultFont);
	$("#designForm").ajaxForm(
			{
				beforeSubmit : function(e) {
					$("#message").html("");
					$("#msgcontainer, #loading").show();
				},
				success : function(e) {
					$("#loading").hide();
					$("#message").html(
							"<span style=\"color:green;\">"
									+ Translation.ChangesWasSaved + "</span>");
					$("#msgcontainer, #loading").hide();
				}
			});
});