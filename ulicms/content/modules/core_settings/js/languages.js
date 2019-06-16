$(function () {
    $(".btn-make-default").click(function (event) {
        event.preventDefault();
        const url = $(event.target).attr("href");
        const message = $(event.target).attr("data-message");
        bootbox.confirm(message,
                function (result) {
                    if (result) {
                        location.replace(url);
                    }
                });

    });
});