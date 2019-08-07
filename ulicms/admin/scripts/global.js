/* global bootbox, PasswordSecurityTranslation, MenuTranslation */

// Internet Exploder caches AJAX requests by default
$(document).ready(() =>
    $.ajaxSetup({cache: false})
);

$(() => {
    const language = $("html").data("select2-language");

    bootbox.setDefaults({
        locale: $("html").data("select2-language")
    });
    // toggle hamburger menu
    $("#menu-toggle").click(() =>
        $(".mainmenu").slideToggle()
    );
    // clear-cache shortcut icon
    $("#menu-clear-cache").click((button) => {
        $(button).hide();
        $("#menu-clear-cache-loading").show();
        const url = $("#menu-clear-cache").data("url");
        $.get(url, () => {
            $("#menu-clear-cache").show();
            $("#menu-clear-cache-loading").hide();
        });
    });

    // Add bootstrap css class to tablesorter
    $.extend($.fn.dataTableExt.oStdClasses, {
        sFilterInput: "form-control",
        sLengthSelect: "form-control"
    });

    $(".select-on-click").click((event) =>
        $(event.target).select()
    );

    initDataTables("body");

    // password security check
    if (typeof $(".password-security-check").password !== "undefined") {
        $(".password-security-check").password({
            shortPass: PasswordSecurityTranslation.ShortPass,
            badPass: PasswordSecurityTranslation.BadPass,
            goodPass: PasswordSecurityTranslation.GoodPass,
            strongPass: PasswordSecurityTranslation.StrongPass,
            containsUsername: PasswordSecurityTranslation.ContainsUsername,
            enterPass: PasswordSecurityTranslation.EnterPass,
            showPercent: false,
            showText: true, // shows the text tips
            animate: true, // whether or not to animate the progress bar on input blur/focus
            animateSpeed: "fast", // the above animation speed
            username: $("[name=username]").length ? $("[name=username]") : false, // select the username field (selector or jQuery instance) for better password checks
            usernamePartialMatch: true, // whether to check for username partials
            minimumLength: 4 // minimum password length (below this threshold, the score is 0)
        });
    }
    // Links to upcoming features

    $(".coming-soon").click((event) => {
        event.preventDefault();
        bootbox.alert("Coming Soon!");
    });
    // Showing a link in an alert box
    initRemoteAlerts("body");

    // There is a bug in iOS Safari's implementation of datetime-local
    // Safari appends a timezone to value on change while the
    // validation only accepts value without timezone
    // remove the timezone from the datetime value
    // https://www.reddit.com/r/webdev/comments/6pxfn3/ios_datetimelocal_inputs_broken_universally/
    $("input[type='datetime-local']").change((event) =>
        event.target.value = event.target.value.substr(0, 16)
    );

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

    // override save shortcut to trigger submit button
    if ($("form button[type=submit], form input[type=submit]").length) {
        document.addEventListener(
                "keydown",
                (e) => {
            if ((window.navigator.platform.match("Mac") ? e.metaKey : e.ctrlKey)
                    && e.keyCode === 83) {
                e.preventDefault();
                $("form button[type=submit], form input[type=submit]")
                        .last()
                        .click();
                // Process the event here (such as click on submit button)
            }
        }, false);
    }

    // prettier select-boxes
    $("select").select2({
        width: "100%",
        language: language
    });

    // Toggle switches for some checkboxes
    $(".js-switch").bootstrapToggle({
        on: MenuTranslation.On,
        off: MenuTranslation.Off
    });

    $.datetimepicker.setLocale(language);

    $(".datepicker").datetimepicker({
        format: "Y-m-d",
        timepicker: false
    });

    // User has to confirm logout
    $("a.backend-menu-item-logout").click((event) => {
        event.preventDefault();
        const url = $(event.target).attr("href");
        bootbox.confirm(`${MenuTranslation.Logout}?`, (result) => {
            if (result) {
                location.href = url;
            }
        });
    });
});
