$(() => {
    $(".has-ajax-content").click((event) => {
        if ($(event.currentTarget).hasClass("loaded")) {
            return;
        }

        const url = $(event.currentTarget).data("url");
        $(event.currentTarget).find(".accordion-content").load(url);

        if (!$(event.currentTarget).hasClass("always-update")) {
            $($(event.currentTarget)).addClass("loaded");
        }
    });

    $("#show_positions").change((event) => {
        setWaitCursor();

        const url = $(event.currentTarget).data("url");
        $.ajax({
            method: "get",
            url: url,
            success: () => {
                setDefaultCursor();
            },
            error: (xhr) => {
                setDefaultCursor();
                alert(xhr.responseText);
            }
        });
    });
});