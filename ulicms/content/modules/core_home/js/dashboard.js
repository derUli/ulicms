$(function () {
    $("#ulicms-newsfeed").click(function (event) {
        if ($(event.target).hasClass("loaded")) {
            return;
        }
        var url = $("#ulicms-feed").data("url");
        $('#ulicms-feed').load(url);
        $(event.target).addClass("loaded")
    });
    var url = $("#patch-notification").data("url");
    $.get(url, function (data) {
        if (data.length > 0) {
            $("#patch-notification #patch-message").html(data);
            $("#patch-notification").slideDown();
        }
    });

    $("#show_positions").change(function (event) {
        var url = $(event.target).data("url");
        $.ajax({
            method: "get",
            url: url,
            error: function (xhr, status, error) {
                alert(xhr.responseText);
            }
        });

    });
});