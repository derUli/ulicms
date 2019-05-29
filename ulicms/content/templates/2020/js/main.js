$.fn.isInViewport = function () {
    var elementTop = $(this).offset().top;
    var elementBottom = elementTop + $(this).outerHeight();
    var viewportTop = $(window).scrollTop();
    var viewportBottom = viewportTop + $(window).height();
    return elementBottom > viewportTop && elementTop < viewportBottom;
};

$(function () {
    $(".footer").last().fadeIn();

    $(".main").onepage_scroll({
        pagination: true,
        animationTime: 2000,

        afterMove: function (index) {
            $($("section .sliding").get(index - 2)).addClass("slide-in");
        },
        loop: false
    });

    $('.button.move-down').click(function () {
        $(".main").moveDown();
    });
});
