/* global Translation */

$(() => {
    $('nav ul:first-child').slicknav({
        "prependTo": "#mobile-nav",
        "label": Translation.Menu,
        "allowParentLinks": true
    });
});