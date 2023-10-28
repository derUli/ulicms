/**
 * Shake animation
 * @param {type} div
 * @returns {undefined}
 */
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

/**
 * Set mouse pointer to wait
 * @returns {undefined}
 */

const setWaitCursor = () => {
    $('body').css('cursor', 'progress');
};

/**
 * Set mouse pointer to default
 * @returns {undefined}
 */
const setDefaultCursor = () => {
    $('body').css('cursor', 'auto');
};

/**
 * Tooltip drop in replacement for title attributes
 * @param string root
 */
const bindTooltips = (root) => {
    if (isTouchDevice()) {
        return;
    }
    $(root).find("*[title]").tooltip();
}


/**
 * Update sticky scroll element

 * @param string selector
 */
const stickyUpdate = (selector) => {
    const element = $(selector);
    
    const padding = 10;
    const scrollY = window.scrollY
    const viewportHeight = window.innerHeight;
    const elementHeight = element.height()

    var elementPosition = scrollY + viewportHeight - elementHeight - padding;

    element.css('position', 'absolute');
    element.css('top', `${elementPosition}px`);
    element.css('width', '100%');
}