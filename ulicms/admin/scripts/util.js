// scrolls to an anchor with animation
function scrollToAnchor(aid) {
	var aTag = $("a[name='" + aid + "']");
	$('html,body').animate({
		scrollTop : aTag.offset().top
	}, 'slow');
}

// shakes a div (animation)
// This is used when login fails
function shake(div) {
	var interval = 100;
	var distance = 10;
	var times = 4;

	$(div).css('position', 'relative');

	for (var iter = 0; iter < (times + 1); iter++) {
		$(div).animate({
			left : ((iter % 2 == 0 ? distance : distance * -1))
		}, interval);
	}// for

	$(div).animate({
		left : 0
	}, interval);

}// shake

// this bind an event to a checkbox to toggle a password field between clear
// text and stars
function bindTogglePassword(input, checkbox) {
	var input = $(input);
	var checkbox = $(checkbox);
	$(checkbox).click(function() {
		if ($(checkbox).is(':checked')) {
			$(input).attr('type', 'text');
		} else {
			$(input).attr('type', 'password');
		}
	});
}

$(function(){
   $(".select-all").change(selectAll);
   $(".checkbox").change(checkboxChecked);
});

function checkboxChecked(event){
	var item = $(event.target).data("select-all-checkbox");
	var group = $(event.target).data("checkbox-group");
	if(!item){
		return;
	}
	var allSelected = $('.checkbox:checked').length == $("input[type=checkbox][data-checkbox-group=" + group +"]").length;
	$(item).prop("checked", allSelected);    
}

function selectAll(event){
	var selectAllCheckbox = $(event.target);
	var target = $(selectAllCheckbox).data("target");
	console.log(target);
	$(target).prop("checked", $(selectAllCheckbox).is(":checked"));
}