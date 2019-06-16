$(function () {
    // Show full text in an alert modal when
    // the user clicks on the shortened text
    $(".ajax-alert").click(function (event) {
        event.preventDefault();
        var url = $(event.target).data("url");
        // do an ajax call
        $.ajax({
            url: url,
            success: function (result) {
                var unread = $(event.target).closest(".unread")
                if (unread.length) {
                    unread.removeClass("unread");
                    var commentCounter = $(".comment-counter .count");
                    var newCount = commentCounter.data("count") - 1;
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