/* global bootbox, PasswordSecurityTranslation, MenuTranslation, zenscroll, vanillaToast, GlobalTranslation */

// Internet Exploder caches AJAX requests by default
$(document).ready(() => {
    $.ajaxSetup({cache: false});

    const token = $("body").data("csrf-token");

    $.ajaxPrefilter(function (options, originalOptions, jqXHR) {
        if (options.type.toLowerCase() === "post") {
            // initialize `data` to empty string if it does not exist
            options.data = options.data || "";

            // add leading ampersand if `data` is non-empty
            options.data += options.data ? "&" : "";

            // add _token entry
            options.data += "csrf_token=" + encodeURIComponent(token);
        }
    });
});

$(() => {
    const language = $("html").data("select2-language");
    bootbox.setDefaults({
        locale: $("html").data("select2-language")
    });

    // toggle hamburger menu
    $("#menu-toggle").click(() =>
        $(".mainmenu").slideToggle()
    );

    $(".mainmenu a.is-not-ajax").click((event) => {                     
        $(".mainmenu").hide();
        
        if(event.target.target === "_blank") {
            return
        }
        
        $("#main-backend-content, #message").hide();
        $("#main-content-loadspinner").show();
    });

    $("a.is-ajax").click((event) => {
        event.preventDefault();
        event.stopPropagation();
        
        const target = $(event.target);
        const url = target.attr("href");
            
        const mainMenu = $(".mainmenu");
        const isMenuEntry = mainMenu.has(target);
     
        $(".mainmenu").hide();

        $("#main-backend-content, #message").hide();
        $("#main-content-loadspinner").show();

        $("#content-container").load(url, (response, status, xhr) => {
            $("#main-backend-content").show();
            $("#main-content-loadspinner").hide();
            if (status === "error") {
                const msg = `${xhr.status} ${xhr.statusText}`;
                bootbox.alert(
                        $('<div/>').text(msg).html()
                        );
            } else if(isMenuEntry) {
               mainMenu.find("a").removeClass("active");
                target.addClass("active");
             }
        });
    });

    // clear-cache shortcut icon
    $("#menu-clear-cache").click((event) => {
        event.preventDefault();
        event.stopPropagation();

        $("#menu-clear-cache").hide();
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

    // copy text from input to clipboard on click
    $(".select-on-click").click((event) => {
        const target = $(event.target);
        copyTextToClipboard(
                target.value,
                () => vanillaToast.success(GlobalTranslation.CopiedToClipboardSuccess),
                () => {
            vanillaToast.error(GlobalTranslation.CopiedToClipboardFailed);
            target.select();
        }
        );
    });

    // Disabled a link-buttons must not be clickable
    $("a").click((event) => {
        const target = $(event.currentTarget);
        if ((target.hasClass("disabled") || target.attr("disabled")) && target.attr("href").length > 1) {
            event.preventDefault();
        }
    });

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

    const isSafari = !!navigator.userAgent.match(/Version\/[\d\.]+.*Safari/);
    if (isSafari) {
        $("input[type='datetime-local']").map((element) =>
            $(element).val($(element).val().substr(0, 16))
        );

        $("input[type='datetime-local']").change((event) =>
            event.target.value = event.target.value.substr(0, 16)
        );
    }

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

    // bootstrap-toggle doesn't react to click on the label of a toggle switch
    // This is a long standing issue that is still not fixed.
    // https://github.com/minhur/bootstrap-toggle/issues/23
    // just wrap the clickable text in an element with this css class
    // to make it clickable
    $(".js-switch-label").click((event) => {
        const target = $(event.target);
        const theSwitch = target.closest('.checkbox').find(".js-switch");
        if (theSwitch && theSwitch.length) {
            theSwitch.bootstrapToggle('toggle');
        }

    });

    $.datetimepicker.setLocale(language);
    $(".datepicker").datetimepicker({
        format: "Y-m-d",
        timepicker: false
    });

    // User has to confirm logout
    $("a.backend-menu-item-logout").click((event) => {
        event.preventDefault();
            $(".mainmenu").hide();
        const url = $(event.target).attr("href");
        bootbox.confirm(`${MenuTranslation.Logout}?`, (result) => {
            if (result) {
                location.href = url;
            }
        });
    });

    // show a scroll-to-top arrow
    // if the scroll viewport isn't at top of the page
    $(window).scroll(() => {
        if ($(window).scrollTop() > 0) {
            $("#scroll-to-top").fadeIn();
        } else {
            $("#scroll-to-top").fadeOut();
        }
    });

    // scroll to top arrow at bottom right
    $("#scroll-to-top").click((event) => {
        event.preventDefault();
        event.stopPropagation();

        zenscroll.toY(0);
    });

    $(".more-options-toggle").click((event) => {
        const target = $(event.target);
        const toggleTarget = $(target.data("target"));

        toggleTarget.slideToggle()

    });
});
