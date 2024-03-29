/* global bootbox */

$(() => {
    bootbox.setDefaults({
        'locale': $("html").data("select2-language")
    });

    $(document).ajaxSend(() => setWaitCursor());
    $(document).ajaxComplete(() => setDefaultCursor());

    // attach handler to form's submit event
    $('.uninstall-form').submit((event) => {
        event.preventDefault();

        message = $(event.target).data("confirm-message");
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

    $(".default-theme-icon").click((event) => {
        const target = $(event.currentTarget);
        const url = target.data("url");

        $.ajax({
            url: url,
            success: () => {
                $(".default-theme-icon").removeClass(
                        "btn-success btn-danger"
                        );

                $(".default-theme-icon").each((index, element) => {
                    const classes = $(element).data("theme") === target.data("theme") ?
                            "btn-success" : "btn-danger";
                    $(element).addClass(classes);
                });
            },
            error: (xhr, status, error) =>
                bootbox.alert(error)
        });
    });

    $(".default-mobile-theme-icon").click((event) => {
        const target = $(event.currentTarget);
        const url = target.data("url");
        const isActive = target.hasClass("btn-success");

        $.ajax({
            url: url,
            success: () => {
                $(".default-mobile-theme-icon").removeClass(
                        "btn-success btn-danger"
                        );

                $(".default-mobile-theme-icon").each((index, element) => {
                    const classes = $(element).data("theme") === target.data("theme") && !isActive ?
                            "btn-success" : "btn-danger";
                    $(element).addClass(classes);
                });
            },
            error: (xhr, status, error) =>
                bootbox.alert(error)
        });
    });

    $(".toggle-module-form").submit((event) => {
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
