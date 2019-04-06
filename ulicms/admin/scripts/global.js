$(document).ready(function () {
    $.ajaxSetup({cache: false});
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

    $('.password-security-check').password({
        shortPass: PasswordSecurityTranslation.ShortPass,
        badPass: PasswordSecurityTranslation.BadPass,
        goodPass: PasswordSecurityTranslation.GoodPass,
        strongPass: PasswordSecurityTranslation.StrongPass,
        containsUsername: PasswordSecurityTranslation.ContainsUsername,
        enterPass: PasswordSecurityTranslation.EnterPass,
        showPercent: false,
        showText: true, // shows the text tips
        animate: true, // whether or not to animate the progress bar on input blur/focus
        animateSpeed: 'fast', // the above animation speed
        username: false, // select the username field (selector or jQuery instance) for better password checks
        usernamePartialMatch: true, // whether to check for username partials
        minimumLength: 4 // minimum password length (below this threshold, the score is 0)
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
