new CookiesEuBanner(() => {
    const url = $("#cookies-eu-banner").data("url");
    const jqxhr = $.ajax(url);
    jqxhr.done(function (html) {
        $("body").append(html);
    });
}, true);