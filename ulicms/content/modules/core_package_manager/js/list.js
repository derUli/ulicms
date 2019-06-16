$(function () {
    bootbox.setDefaults({
        'locale': $("html").data("select2-language")
    });

    $(document).ajaxSend(function () {
        setWaitCursor();
    });
    $(document).ajaxComplete(function () {
        setDefaultCursor();
    });

    // attach handler to form's submit event
    $('.uninstall-form').submit(function (event) {
        message = $(event.target).data("confirm-message");

        event.preventDefault();

        bootbox.confirm(message, function (result) {
            if (result) {
                var form = $(event.target);
                // submit the form
                $(form).ajaxSubmit({
                    success: function (result) {
                        // hide and remove the table row of the uninstalled
                        // package
                        $(form).closest("tr").fadeOut(400, function () {
                            $(form).closest("tr").remove();
                        });
                    },
                    error: function (xhr, status, error) {
                        bootbox.alert(error);
                    }
                });
            }
        });
    });
    $('#truncate-installed-patches').submit(function (event) {
        message = $(event.target).data("confirm-message");

        event.preventDefault();

        bootbox.confirm(message, function (result) {
            if (result) {
                var form = $(event.target);
                // submit the form
                $(form).ajaxSubmit({
                    success: function (result) {
                        $("#patch-list tbody tr").remove();
                    },
                    error: function (xhr, status, error) {
                        bootbox.alert(error);
                    }
                });
            }
        });
    });
    $(".toggle-module-form")
            .submit(
                    function (event) {

                        event.preventDefault();

                        var form = $(event.target);
                        // submit the form
                        $(form).ajaxSubmit(
                                {
                                    success: function (result) {
                                        // hide and remove the table row
                                        // of the
                                        // uninstalled
                                        // package
                                        console.log("[data-btn-for="
                                                + result["name"] + "]");
                                        var settingsButton = $(
                                                "[data-btn-for="
                                                + result["name"]
                                                + "]").not(
                                                ".has-no-settings");
                                        if (result["enabled"]) {
                                            $(form).find(".btn-enable").hide();
                                            $(form).find(".btn-disable")
                                                    .show();
                                            $(settingsButton).attr(
                                                    "disabled", null);
                                            $(settingsButton)
                                                    .removeClass("disabled");
                                        } else {
                                            $(form).find(".btn-enable").show();
                                            $(form)
                                                    .find(".btn-disable")
                                                    .hide();
                                            $(settingsButton).attr("disabled",
                                                    "disabled");

                                            $(settingsButton).addClass("disabled");
                                        }

                                    },
                                    error: function (xhr, status, error) {
                                        bootbox.alert(error);
                                    }
                                });
                    });
});
