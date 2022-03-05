let headline = document.querySelector('header.header .headline h1');
let logo = document.querySelector('header.header .logo-wrapper');

let initialTruncated = isTruncated(headline);

function updateCursor() {
    if (!logo.classList.contains('hide')) {
        initialTruncated = isTruncated(headline);
        headline.style.cursor = initialTruncated ? 'help' : 'default';
    }
}

window.addEventListener('resize', updateCursor);

updateCursor();

headline.addEventListener('click', (e) => {

    if (!logo || !initialTruncated) {
        return;
    }
    logo.classList.toggle('hide');

    if (!logo.classList.contains('hide')) {
        headline.style.fontSize = '';
        return;
    }

    for (let i = 36; i >= 12; i--) {
        if (isTruncated(headline)) {
            headline.style.fontSize = `${i}px`;
        }
    }

});

/**
 * Check if an element is truncated.
 */
function isTruncated(el) {
    return el.scrollWidth > el.clientWidth
}