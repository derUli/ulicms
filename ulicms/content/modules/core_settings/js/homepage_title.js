/* global Translation */

$("#homepage_title_settings")
        .ajaxForm(
                {
                    beforeSubmit: () => {
                        $("#message").html("");
                        $("#loading").show();
                    },
                    success: () => {
                        $("#loading").hide();
                        $("#message")
                                .html(`<span style="color:green;">
                        ${Translation.ChangesWasSaved}
                </span>`);
                    }
                });
