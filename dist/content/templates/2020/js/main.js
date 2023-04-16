// scroll to the given anchor
const params = new URLSearchParams(location.search);
const dir = location.href.substring(0,
        location.href.lastIndexOf('/'));

// redirect direct page urls to anchors
const jumpTo = params.get('jumpto');

if (jumpTo && jumpTo.length > 0) {
    $("body").css("opacity", "0");
    location.replace(`${dir}/#${jumpTo}`);
}

$(() => {
    $("footer").last().fadeIn();

    new fullpage('#fullpage', {
        afterLoad: (origin, destination) => {
            $(destination.item).find(".sliding").addClass("slide-in");
            $(destination.item).find(".text-content").addClass("move-up");
        },
        anchors: $("#fullpage").data("slugs").split("||"),
        navigationTooltips: $("#fullpage").data("titles").split("||"),
        navigation: true,
        navigationPosition: 'right',
        verticalCentered: false,
        licenseKey: ''
    });
});
