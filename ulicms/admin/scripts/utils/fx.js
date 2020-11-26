// shakes a div (animation)
// This is used when login fails
const shake = (div) => {
    const interval = 100;
    const distance = 10;
    const times = 4;
    $(div).css('position', 'relative');

    for (let iter = 0; iter < (times + 1); iter++) {
        $(div).animate({
            left: ((iter % 2 === 0 ? distance : distance * -1))
        }, interval);
    }// for

    $(div).animate({
        left: 0
    }, interval);
};

// scrolls to an anchor with animation
const scrollToAnchor = (aid) => {
    const aTag = $("a[name='" + aid + "']");
    $('html,body').animate({
        scrollTop: aTag.offset().top
    }, 'slow');
};

const setWaitCursor = () => {
    $('body').css('cursor', 'progress');
};

const setDefaultCursor = () => {
    $('body').css('cursor', 'auto');
};

const bindTooltips = (root) => {
    if (isTouchDevice()) {
        return;
    }
    $(root).find("*[title]").tooltip();
}