$(function () {
    $("footer").last().fadeIn();

    new fullpage('#fullpage', {
        afterLoad: function (origin, destination, direction) {
            $(destination.item).find(".sliding").addClass("slide-in");
            $(destination.item).find(".text-content").addClass("move-up");
        },
        anchors: $("#fullpage").data("slugs").split("||"),
        navigationTooltips: $("#fullpage").data("titles").split("||"),
        navigation: true,
        navigationPosition: 'right',
        verticalCentered: false
    });

});
