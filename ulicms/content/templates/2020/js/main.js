$(function () {
    $(".main").onepage_scroll({
        pagination: true
    });
    $('.button.move-down').click(function () {
        $(".main").moveDown();
    });
});
