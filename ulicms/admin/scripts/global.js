$(document).ready(function () {
    $.ajaxSetup({cache: false});

    // It seems like the autocomplete html attribute
    // is broken in some modern browsers (Chrome)
    // This is an ugly workaround for that issue
    $("form input[autocomplete], form[autocomplete]")
            .not(":disabled")
            .prop("disabled", true);
    setTimeout(function () {
        $("form input[autocomplete], form[autocomplete] input").prop(
                "disabled",
                false
                );
    }, 800);
});

// TODO: Das hier in kleinere Blöcke zerhacken
$(function () {
    var language = $("html").data("select2-language");
    // toggle hamburger menu
    $("#menu-toggle").click(function () {
        $(".mainmenu").slideToggle();
    });
    // clear-cache shortcut icon
    $("#menu-clear-cache").click(function () {
        $(this).hide();
        $("#menu-clear-cache-loading").show();
        var url = $("#menu-clear-cache").data("url");
        $.get(url, function (result) {
            $("#menu-clear-cache").show();
            $("#menu-clear-cache-loading").hide();
        });
    });

    // Add bootstrap css class to tablesorter
    $.extend($.fn.dataTableExt.oStdClasses, {
        sFilterInput: "form-control",
        sLengthSelect: "form-control"
    });

    $(".tablesorter").DataTable({
        language: {
            url: $("body").data("datatables-translation")
        },
        columnDefs: [{targets: "no-sort", orderable: false}]
    });

    var editors = $(".ckeditor");
    editors.each(function (index, element) {
        ClassicEditor
                .create(element)
                .then(editor => {
                    window.editor = editor;
                })
                .catch(err => {
                    console.error(err.stack);
                });
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
    $("input, select, textarea")
            .not("input[type=checkbox]")
            .not("input[type=radio]")
            .not("input[type=button]")
            .not("input[type=submit]")
            .not("input[type=reset]")
            .not("input[type=image]")
            .addClass("form-control");

    // override save shor
    // tcut to trigger submit button
    if ($("form button[type=submit], form input[type=submit]").length) {
        document.addEventListener("keydown", function (e) {
            if ((window.navigator.platform.match("Mac") ? e.metaKey : e.ctrlKey) && e.keyCode == 83) {
                e.preventDefault();
                $("form button[type=submit], form input[type=submit]").last().click();
                // Process the event here (such as click on submit button)
            }
        }, false);
    }

    // prettier select-boxes
    $("select").select2({
        width: "100%",
        language: language
    });
    $(".js-switch").bootstrapToggle({
        on: MenuTranslation.On,
        off: MenuTranslation.Off
    });

    $.datetimepicker.setLocale(language);

    $(".datepicker").datetimepicker({
        format: "Y-m-d",
        timepicker: false
    });

    $("a.backend-menu-item-logout").click(function (event) {
        if (!window.confirm(MenuTranslation.Logout + "?")) {
            event.preventDefault();
        }
    });
});
