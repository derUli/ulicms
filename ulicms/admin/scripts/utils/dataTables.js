
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

    $(`#${settings.sInstance}`).find("a.show-children").click((event) => {
        event.preventDefault();
        event.stopPropagation();
        const id = $(event.target).data("id");
        $("#filter_parent").val(id).trigger("change");
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
