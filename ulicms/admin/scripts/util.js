// scrolls to an anchor with animation
function scrollToAnchor(aid) {
    var aTag = $("a[name='" + aid + "']");
    $('html,body').animate({
        scrollTop: aTag.offset().top
    }, 'slow');
}

function refreshCodeMirrors() {
    $('.CodeMirror').each(function (i, el) {
        el.CodeMirror.refresh();
    });
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
            left: ((iter % 2 == 0 ? distance : distance * -1))
        }, interval);
    }// for

    $(div).animate({
        left: 0
    }, interval);

}// shake

// this bind an event to a checkbox to toggle a password field between clear
// text and stars
function bindTogglePassword(input, checkbox) {
    var input = $(input);
    var checkbox = $(checkbox);
    $(checkbox).click(function () {
        if ($(checkbox).is(':checked')) {
            $(input).attr('type', 'text');
        } else {
            $(input).attr('type', 'password');
        }
    });
}

$(function () {
    $("input[type=checkbox].select-all").change(selectAllChecked);
    $("input[type=checkbox]").change(checkboxChecked);

    // check "Select All" checkbox if all checkboxes of this group are checked
    $("input[type=checkbox]").each(function (index, target) {
        var item = $(target).data("select-all-checkbox");
        var group = $(target).data("checkbox-group");
        if (item != null && group != null) {
            checkSelectAllIfAllChecked(item, group);
        }
    });

    // scroll to the given anchor
    var jumpTo = url("?jumpto");
    if (typeof jumpTo !== "undefined" && jumpTo.length > 0) {
        location.href = "#" + jumpTo;
    }
});

function checkboxChecked(event) {
    var item = $(event.target).data("select-all-checkbox");
    var group = $(event.target).data("checkbox-group");
    checkSelectAllIfAllChecked(item, group);
}

function checkSelectAllIfAllChecked(item, group) {
    if (!item) {
        return;
    }
    // if the count of the checked checkboxes in the group is equal to the count
    // of all checkboxes in this group
    var allSelected = $("input[type=checkbox][data-checkbox-group='" + group
            + "']:checked").length == $("input[type=checkbox][data-checkbox-group='"
            + group + "']").length;
    // check the "Select All" Checkbox, else uncheck it
    $(item).prop("checked", allSelected).change();
}

function selectAllChecked(event) {
    var selectAllCheckbox = $(event.target);
    var target = $(selectAllCheckbox).data("target");
    $(target).prop("checked", $(selectAllCheckbox).is(":checked")).change();
}

function setWaitCursor() {
    $('body').css('cursor', 'progress');
}
function setDefaultCursor() {
    $('body').css('cursor', 'auto');
}

function isTouchDevice() {
    var prefixes = ' -webkit- -moz- -o- -ms- '.split(' ');
    var mq = function (query) {
        return window.matchMedia(query).matches;
    }

    if (('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch) {
        return true;
    }

    // include the 'heartz' as a way to have a non matching MQ to help terminate the join
    // https://git.io/vznFH
    var query = ['(', prefixes.join('touch-enabled),('), 'heartz', ')'].join('');
    return mq(query);
}