/* global bootbox */

$(() => {
    bootbox.setDefaults({
        'locale': $("html").data("select2-language")
    });

    $(document).ajaxSend(() => setWaitCursor());
    $(document).ajaxComplete(() => setDefaultCursor());

    // attach handler to form's submit event
    $('.uninstall-form').submit((event) => {
        message = $(event.target).data("confirm-message");

        event.preventDefault();

        bootbox.confirm(message, (result) => {
            if (result) {
                const form = $(event.target);
                // submit the form
                $(form).ajaxSubmit({
                    success: () => {
                        // hide and remove the table row of the uninstalled
                        // package
                        $(form).closest("tr").fadeOut(400, () =>
                            $(form).closest("tr").remove()
                        );
                    },
                    error: (xhr, status, error) =>
                        bootbox.alert(error)
                });
            }
        });
    });
    $('#truncate-installed-patches').submit((event) => {
        message = $(event.target).data("confirm-message");

        event.preventDefault();

        bootbox.confirm(message, (result) => {
            if (result) {
                const form = $(event.target);
                // submit the form
                $(form).ajaxSubmit({
                    success: (result) =>
                        $("#patch-list tbody tr").remove()
                    ,
                    error: (xhr, status, error) =>
                        bootbox.alert(error)
                });
            }
        });
    });
    $(".toggle-module-form")
            .submit((event) => {
                event.preventDefault();
                const form = $(event.target);
                // submit the form
                $(form).ajaxSubmit(
                        {
                            success: (result) => {
                                // hide and remove the table row
                                // of the
                                // uninstalled
                                // package
                                console.log("[data-btn-for="
                                        + result["name"] + "]");
                                const settingsButton = $(
                                        "[data-btn-for="
                                        + result["name"]
                                        + "]").not(
                                        ".has-no-settings");

                                if (result["enabled"]) {
                                    $(form).find(".btn-enable").hide();
                                    $(form).find(".btn-disable").show();
                                    $(settingsButton).attr("disabled", null);
                                    $(settingsButton).removeClass("disabled");
                                } else {
                                    $(form).find(".btn-enable").show();
                                    $(form).find(".btn-disable").hide();
                                    $(settingsButton).attr("disabled",
                                            "disabled");

                                    $(settingsButton).addClass("disabled");
                                }

                            },
                            error: (xhr, status, error) =>
                                bootbox.alert(error)
                        });
            });
});
