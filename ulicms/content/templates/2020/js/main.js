$(function () {
    $("footer").last().fadeIn();

    $(".main").onepage_scroll({
        pagination: true,
        animationTime: 2000,

        afterMove: function (index) {
            $($("section .sliding").get(index - 2)).addClass("slide-in");
            $($("section .text-content").get(index - 2)).addClass("move-up");
        },
        loop: false
    });

    $('.button.move-down').click(function () {
        $(".main").moveDown();
    });
});
