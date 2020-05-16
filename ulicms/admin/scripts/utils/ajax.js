const bindAjaxLinks = (root) => {
    $(root).find("a.is-not-ajax").click((event) => {
        $(".mainmenu").hide();
        if (event.target.target === "_blank") {
            return;
        }

        $("#main-backend-content, #message").hide();
        $("#main-content-loadspinner").show();
    });

    $(root).find("a.is-ajax").click((event) => {
        event.preventDefault();
        event.stopPropagation();
        const target = $(event.currentTarget);
        const url = `${target.attr("href")}&only_content=true`;
        const mainMenu = $(".mainmenu");
        const isMenuEntry = mainMenu.has(target);
        $(".mainmenu").hide();
        $("#main-backend-content, #message").hide();
        $("#main-content-loadspinner").show();
        $("#content-container").load(url, (response, status, xhr) => {
            $("#main-backend-content").show();
            $("#main-content-loadspinner").hide();
            if (status === "error") {
                const msg = `${xhr.status} ${xhr.statusText}`;
                bootbox.alert(
                        $('<div/>').text(msg).html()
                        );
            } else if (isMenuEntry) {
                mainMenu.find("a").removeClass("active");
                target.addClass("active");
                bindAjaxLinks($("#content-container"));
            }
        });
    });
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