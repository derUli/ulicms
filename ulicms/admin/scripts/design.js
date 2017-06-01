function initMobileDetectNotice() {
	if ($("select[name='mobile_theme']").val() != ""
			&& $("#mobile_detect_notice").data("installed") == false) {
		$("#mobile_detect_notice").slideDown();
	} else {
		$("#mobile_detect_notice").slideUp();
	}
}

$(function() {
	$("#mobile_detect_notice").hide();
	initMobileDetectNotice();
	$("select[name='mobile_theme']").change(function() {
		initMobileDetectNotice();
	});
});