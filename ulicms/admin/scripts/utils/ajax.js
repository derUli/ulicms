const bindAjaxLinks = (root) => {
    $(root).find("a.is-not-ajax").click((event) => {
        $(".mainmenu").hide();
        if (event.target.target === "_blank") {
            return;
        }

        ajaxLoadSpinner.show();
    });

    $(root).find("a.is-ajax").click((event) => {
        event.preventDefault();
        event.stopPropagation();

        const target = $(event.currentTarget);
        const originalUrl = target.attr("href");
        let url = `${originalUrl}&only_content=true`;

        if (target.hasClass("full-minified")) {
            url += "&full_minified=true"
        }

        const mainMenu = $(".mainmenu");
        const isMenuEntry = mainMenu.has(target);

        mainMenu.hide();
        ajaxLoadSpinner.show();

        const contentContainer = $("#content-container");
        $(contentContainer).load(url, (response, status, xhr) => {
            ajaxLoadSpinner.hide();
            if (status === "error") {
                const msg = `${xhr.status} ${xhr.statusText}`;
                bootbox.alert(
                        $('<div/>').text(msg).html());
                return;
            } else if (isMenuEntry) {
                mainMenu.find("a").removeClass("active");
                target.addClass("active");
            }
            history.pushState({ajaxUrl: url}, document.title, originalUrl);
            bindContentEvents(contentContainer);
            initDataTables(contentContainer);
        });
    });
};

const ajaxGoTo = (url) => {
    $("#main-backend-content, #message").hide();
    $("#main-content-loadspinner").show();
    const contentContainer = $("#content-container");
    $(contentContainer).load(url, (response, status, xhr) => {

        $("#main-backend-content").show();
        $("#main-content-loadspinner").hide();

        if (status === "error") {
            const msg = `${xhr.status} ${xhr.statusText}`;
            bootbox.alert(
                    $('<div/>').text(msg).html());
            return;
        }

        bindContentEvents(contentContainer);
    });
};

const bindContentEvents = (contentContainer) => {
    bindAjaxLinks(contentContainer);
    initRemoteAlerts(contentContainer);
    initSelect2(contentContainer);
    addCssClassToInputs(contentContainer);
    initBootstrapToggle(contentContainer);
    bindTooltips($("body"));
};

const initRemoteAlerts = (rootElement) => {
    $(rootElement).find(".remote-alert").click((event) => {
        event.preventDefault();
        event.stopPropagation();

        setWaitCursor();
        const url = $(event.currentTarget).data("url");
        $.get(url, (result) => {
            setDefaultCursor();
            bootbox.alert(result);
        });
    });
};

const ajaxLoadSpinner = {
    show: () => {
        $("#main-content-loadspinner, #message").show();
        $("#main-backend-content").hide();
    },
    hide: () => {
        $("#main-content-loadspinner, #message").hide();
        $("#main-backend-content").show();
    }
}