// When the page is completely loaded.

const initFX = () => {
    if (detectRobot()) {
        return;
    }

    let container = $(".root");
    container.css('opacity', 0.0);
    container.fadeTo('slow', 1.0);
}

$(document).ready(initFX);