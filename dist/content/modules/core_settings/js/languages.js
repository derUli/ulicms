/* global bootbox */

$(() => {
    $(".btn-make-default").click((event) => {
        event.preventDefault();
        const url = $(event.target).attr("href");
        const message = $(event.target).attr("data-message");
        bootbox.confirm(message,
                (result) => {
            if (result) {
                location.replace(url);
            }
        });
    });
});
