/* global bootbox, PasswordSecurityTranslation, MenuTranslation, zenscroll, vanillaToast, GlobalTranslation */
// Internet Exploder caches AJAX requests by default
$(document).ready(() => {
    const body = $("body");

    $.ajaxSetup(
            {
                cache: false,
                beforeSend: (jqXHR, settings) => {
                    // set url to get in the general error handler
                    jqXHR.url = settings.url;
                },
                error: (jqXHR) => {
                    const errorMessage =
                            `Error requesting ${jqXHR.url} - ${jqXHR.status} ${jqXHR.statusText}`
                    console.error(errorMessage)
                    vanillaToast.error(errorMessage);
                }
            }
    );

    const token = $(body).data("csrf-token");
    $.ajaxPrefilter((options, originalOptions, jqXHR) => {
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
    const body = $("body");
    const language = $("html").data("select2-language");
    bootbox.setDefaults({
        locale: $("html").data("select2-language")
    });

    // toggle hamburger menu
    $("#menu-toggle").click(() =>
        $(".mainmenu").slideToggle()
    );

    bindAjaxLinks(body);

    // handle browser back events (Required for ajax loaded pages)
    $(window).bind('popstate', (event) => {
        const state = event.originalEvent.state;

        console.log('popstate', state);

        // if there is no state this history entry
        // was a full page reload
        if (!state) {
            ajaxLoadSpinner.show();
            location.replace(document.location);
            return;
        }
        // if this page was loaded by ajax
        // use ajax again
        if (typeof state.ajaxUrl === 'string') {
            console.log('go back to', state.ajaxUrl);
            ajaxGoTo(state.ajaxUrl);
            return;
        }
        // If there is a state but no ajax URL
        // then this history entry was a full page reload
        // Then reload page

        ajaxLoadSpinner.show();
        location.replace(window.location.href);
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
        const target = event.target;
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

    initDataTables(body);
    initPasswordSecurityCheck(body);

    // Links to upcoming features
    $(".coming-soon").click((event) => {
        event.preventDefault();
        bootbox.alert("Coming Soon!");
    });

    // Showing a link in an alert box
    initRemoteAlerts(body);

    addCssClassToInputs($(body));

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
            }
        }, false);
    }

    $("input[type=checkbox] .select-all").change(selectAllChecked);
    $("input[type=checkbox]").change(checkboxChecked);

    // check "Select All" checkbox if all checkboxes of this group are checked
    $("input[type=checkbox]").each((index, target) => {
        const item = $(target).data("select-all-checkbox");
        const group = $(target).data("checkbox-group");
        if (item !== null && group !== null) {
            checkSelectAllIfAllChecked(item, group);
        }
    });

    // scroll to the given anchor
    const params = new URLSearchParams(location.search);
    const jumpTo = params.get('jumpto');
    if (jumpTo && jumpTo.length > 0) {
        const anchor = document.querySelector(`#${jumpTo}`);
        if (anchor) {
            zenscroll.to(anchor);
        }
    }

    initSelect2(body);
    initBootstrapToggle(body);

    $.datetimepicker.setLocale(language);

    $(".datepicker").datetimepicker({
        format: "Y-m-d",
        timepicker: false
    });

    $(".datetimepicker").prop("readonly", true);
    $(".datetimepicker").datetimepicker({
        format: "Y-m-d H:i",
        timepicker: true,
        step: 30
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

});