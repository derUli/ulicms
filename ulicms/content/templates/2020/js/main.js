$(function () {
    $(".main").onepage_scroll({
        pagination: false
    });
    $('.button.move-down').click(function () {
        $(".main").moveDown();
    });
});
