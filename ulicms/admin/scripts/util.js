/* global bootbox */

$(() => {
    $("input[type=checkbox].select-all").change(selectAllChecked);
    $("input[type=checkbox]").change(checkboxChecked);
    // check "Select All" checkbox if all checkboxes of this group are checked
    $("input[type=checkbox]").each((index, target) => {
        const item = $(target).data("select-all-checkbox");
        const group = $(target).data("checkbox-group");
        if (item !== null && group !== null) {
            checkSelectAllIfAllChecked(item, group);
        }
    });

    // scroll to the given anchor
    const params = new URLSearchParams(location.search);
    const jumpTo = params.get('jumpto');
    if (jumpTo && jumpTo.length > 0) {
        location.href = "#" + jumpTo;
    }
});

// scrolls to an anchor with animation
const scrollToAnchor = (aid) => {
    const aTag = $("a[name='" + aid + "']");
    $('html,body').animate({
        scrollTop: aTag.offset().top
    }, 'slow');
};
const refreshCodeMirrors = () => {
    $('.CodeMirror').each((i, el) =>
        el.CodeMirror.refresh()
    );
};

const copyTextToClipboard = (
        text,
        successCallback = null,
        errorCallback = null
        ) => {
    const textArea = document.createElement("textarea");

    //
    // *** This styling is an extra step which is likely not required. ***
    //
    // Why is it here? To ensure:
    // 1. the element is able to have focus and selection.
    // 2. if element was to flash render it has minimal visual impact.
    // 3. less flakyness with selection and copying which **might** occur if
    //    the textarea element is not visible.
    //
    // The likelihood is the element won't even render, not even a
    // flash, so some of these are just precautions. However in
    // Internet Explorer the element is visible whilst the popup
    // box asking the user for permission for the web page to
    // copy to the clipboard.
    //

    // Place in top-left corner of screen regardless of scroll position.
    textArea.style.position = 'fixed';
    textArea.style.top = 0;
    textArea.style.left = 0;

    // Ensure it has a small width and height. Setting to 1px / 1em
    // doesn't work as this gives a negative w/h on some browsers.
    textArea.style.width = '2em';
    textArea.style.height = '2em';

    // We don't need padding, reducing the size if it does flash render.
    textArea.style.padding = 0;

    // Clean up any borders.
    textArea.style.border = 'none';
    textArea.style.outline = 'none';
    textArea.style.boxShadow = 'none';

    // Avoid flash of white box if rendered for any reason.
    textArea.style.background = 'transparent';

    textArea.value = text;

    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        var successful = document.execCommand('copy');
        var msg = successful ? 'successful' : 'unsuccessful';
        console.log('Copying text command was ' + msg);
        if (successCallback) {
            successCallback();
        }
    } catch (err) {
        console.log('Oops, unable to copy');
        if (errorCallback) {
            errorCallback(error);
        }
    }

    document.body.removeChild(textArea);
};

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

// this bind an event to a checkbox to toggle a password field between clear
// text and stars
const bindTogglePassword = (inputField, checkboxField) => {
    const input = $(inputField);
    const checkbox = $(checkboxField);
    $(checkbox).click(() => {
        if ($(checkbox).is(':checked')) {
            $(input).attr('type', 'text');
        } else {
            $(input).attr('type', 'password');
        }
    });
};

const checkboxChecked = (event) => {
    const item = $(event.currentTarget).data("select-all-checkbox");
    const group = $(event.currentTarget).data("checkbox-group");
    checkSelectAllIfAllChecked(item, group);
};

const checkSelectAllIfAllChecked = (item, group) => {
    if (!item) {
        return;
    }

    // if the count of the checked checkboxes in the group is equal to the count
    // of all checkboxes in this group
    const allSelected = $("input[type=checkbox][data-checkbox-group='" + group
            + "']:checked").length === $("input[type=checkbox][data-checkbox-group='"
            + group + "']").length;
    // check the "Select All" Checkbox, else uncheck it
    $(item).prop("checked", allSelected);
};

const selectAllChecked = (event) => {
    const selectAllCheckbox = $(event.target);
    const target = $(selectAllCheckbox).data("target");
    $(target).prop("checked",
            $(selectAllCheckbox).
            is(":checked")).change();
};

const setWaitCursor = () => {
    $('body').css('cursor', 'progress');
};

const setDefaultCursor = () => {
    $('body').css('cursor', 'auto');
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

const dataTableDrawCallback = (settings) => {
    // if the current page doesn't exists go to the last existing page
    const table = $(`#${settings.sInstance}`).DataTable();
    const totalPageCount = table.page.info().pages;
    const currentPage = table.page() + 1;
    if (currentPage > totalPageCount) {
        table.page(totalPageCount);
    }

    $(`#${settings.sInstance}`).find("a.btn").click(
            (event) => {
        const target = $(event.currentTarget);

        if ((target.hasClass("disabled") ||
                target.attr("disabled")) &&
                target.attr("href").length > 1) {
            event.preventDefault();
            event.stopPropagation();
            return;
        }
    });

    $(`#${settings.sInstance}`).find("a.delete-icon").click((event) => {
        event.preventDefault();
        event.stopPropagation();

        const target = $(event.currentTarget);
        const confirmMessage = target.data("confirm");
        const url = target.data("url");
        bootbox.confirm(confirmMessage, (result) => {
            if (result) {
                $.ajax({
                    url,
                    method: 'POST',
                    success: () => {
                        const table = $(`#${settings.sInstance}`);
                        const dataTable = table.DataTable();
                        const row = target.closest("tr");
                        $(row).fadeOut(400, () => {
                            dataTable.row(row).remove().draw(false);
                        });
                    }
                });
            }
        });
    }
    );
};

const prepareSearchData = (data) => {
    data.filters = buildFiltersObject();
    console.log('search data', data);
};

const buildFiltersObject = () => {
    const type = $("#filter_type").val();
    const categoryId = $("#filter_category").val();
    const parentId = $("#filter_parent").val();
    const approved = $("#filter_approved").val();
    const language = $("#filter_language").val();
    const menu = $("#filter_menu").val();
    const active = $("#filter_active").val();

    // TODO: reimplement all filters
    return {
        type,
        category_id: categoryId,
        parent_id: parentId,
        approved: approved,
        language: language,
        menu: menu,
        active: active
    };
};

const initDataTables = (rootElement) => {
    // Sortable and searchable tables
    // Internet Exploder doesn't support URLSearchParams,
    // but which caveman are still using IE?
    // Fuck IE, Fuck Microsuck since Windows 8
    const urlParams = new URLSearchParams(window.location.search);
    // get current action from url
    // this is used as identifier when saving and loading the state
    const action = urlParams.get('action');
    $(rootElement).find(".tablesorter").each((index, element) => {
        const table = $(element);
        const url = table.data("url");

        $(element).DataTable({
            language: {
                url: $("body").data("datatables-translation")
            },
            processing: !!url,
            serverSide: !!url,
            search: (event) => {
                $(event.target).DataTable().page(1);
            },
            ajax: url ? {
                url,
                type: 'GET',
                data: prepareSearchData
            } : null,
            deferRender: true,
            stateSave: true,
            stateDuration: 0,
            stateSaveCallback: (settings, data) => {
                console.log(settings, data);
                localStorage.setItem(
                        "DataTables_" + action + "_"
                        + settings.sInstance, JSON.stringify(data)
                        );
            },
            stateLoadCallback: (settings) =>
                JSON.parse(
                        localStorage.getItem(
                                "DataTables_" + action + "_" + settings.sInstance)
                        )
            ,
            drawCallback: dataTableDrawCallback,
            columnDefs: [{targets: "no-sort", orderable: false}]
        });
    });
};
