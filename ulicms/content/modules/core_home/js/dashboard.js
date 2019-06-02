$(function () {
    $(".has-ajax-content").click(function (event) {
        if ($(event.target).hasClass("loaded")) {
            return;
        }
        var url = $(this).data("url");
        $(this).find(".accordion-content").load(url);
        if (!$(this).hasClass("always-update")) {
            $($(this)).addClass("loaded");
        }
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