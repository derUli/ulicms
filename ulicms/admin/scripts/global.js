$(function () {
    var language = $("html").data("select2-language");

    // toggle hamburger menu
    $("#menu-toggle").click(function () {
        $(".mainmenu").slideToggle();
    });
    // clear-cache shortcut icon
    $("#menu-clear-cache").click(function () {
        $(this).hide();
        $("#menu-clear-cache-loading").show()
        var url = $("#menu-clear-cache").data("url");
        $.get(url, function (result) {
            $("#menu-clear-cache").show();
            $("#menu-clear-cache-loading").hide();
        });
    });

    // Add bootstrap css class to tablesorter
    $.extend($.fn.dataTableExt.oStdClasses, {
        "sFilterInput": "form-control",
        "sLengthSelect": "form-control"
    });

    $(".tablesorter").DataTable({
        language: {
            url: $("body").data("datatables-translation")
        }
    });
    $(".coming-soon").click(function (event) {
        event.preventDefault();
        bootbox.alert("Coming Soon!");
    });
    $(".remote-alert").click(function (event) {
        event.preventDefault();
        setWaitCursor();
        var url = $(this).data("url");
        $.get(url, function (result) {
            setDefaultCursor();
            bootbox.alert(result);
        });
    });

    // dynamically add class form-control to all form elements to
    // make inputs prettier
    $('input, select, textarea').not("input[type=checkbox]").not(
            "input[type=radio]").not("input[type=button]").not(
            "input[type=submit]").not("input[type=reset]").not(
            "input[type=image]").addClass('form-control');

    // There is a bug in iOS Safari's implementation of datetime-local
    // Safari appends a timezone to value on change while the
    // validation only accepts value without timezone
    // remove the timezone from the datetime value
    // https://www.reddit.com/r/webdev/comments/6pxfn3/ios_datetimelocal_inputs_broken_universally/
    $("input[type='datetime-local']").change(function (event) {
        event.target.value = event.target.value.substr(0, 16);
    });

    // prettier select-boxes
    $("select").select2({
        width: '100%',
        language: $("html").data("select2-language")
    });
    $.datetimepicker.setLocale(language);

    $(".datepicker").datetimepicker({
        format: 'Y-m-d',
        timepicker: false
    });
    $("a.backend-menu-item-logout").click(function (event) {
        if (!window.confirm(MenuTranslation.Logout + "?")) {
            event.preventDefault();
        }

    });

});
