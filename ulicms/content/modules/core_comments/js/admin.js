/* global bootbox */

$(() => {
    // Show full text in an alert modal when
    // the user clicks on the shortened text
    $(".ajax-alert").click((event) => {
        event.preventDefault();
        const url = $(event.target).data("url");
        // do an ajax call
        $.ajax({
            url: url,
            success: (result) => {
                const unread = $(event.target).closest(".unread");

                if (unread.length) {
                    unread.removeClass("unread");
                    const commentCounter = $(".comment-counter .count");
                    const newCount = commentCounter.data("count") - 1;
                    commentCounter.data("count", newCount);
                    commentCounter.text(newCount);

                    if (newCount <= 0) {
                        commentCounter.hide();
                    }
                }
                // show the response to the user in an bootbox alert
                bootbox.alert({
                    message: result,
                    size: "large"
                });
            }
        });
    });
});