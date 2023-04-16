$("form#default_edit_restrictions").ajaxForm(
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
