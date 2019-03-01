$(document).ready(function () {
    $.ajaxSetup({cache: false});

    // It seems like the autocomplete html attribute
    // is broken in some modern browsers (Chrome)
    // This is an ugly workaround for that issue
    $("form input[autocomplete], form[autocomplete]").not(":disabled").prop("disabled", true);
    setTimeout(function () {
        $("form input[autocomplete], form[autocomplete] input").prop("disabled", false);
    }, 800);
});

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
        },
        columnDefs: [
            {targets: 'no-sort', orderable: false}
        ]

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
