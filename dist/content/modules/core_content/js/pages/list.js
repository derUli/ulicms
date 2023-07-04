/* global Translation, bootbox */

function onCreatePage(event) {
    event.preventDefault();
    event.stopPropagation();

    const url = $(event.target).data('url');
    const language = $('#filter_language').val();
    const category_id = $('#filter_category').val();
    const parent_id = $('#filter_parent').val();
    const type = $('#filter_type').val();
    const menu = $('#filter_menu').val();

    bootbox.prompt(`${Translation.PageTitle}:`,
    (title) => {
        if(title === null) {
            return;
        }

        if(!title.length){
            onCreatePage(event);
            return;
        }
        
        const data = {
            title: title,
            language: language,
            category_id: category_id,
            menu: menu,
            parent_id: parent_id,
            type: type
        };

        $.ajax({
            url: url,
            data: data,
            type: 'POST',
            success: (response) => {
                console.log(response);
                location.href = response.url;
            },
            error: (xhr, status, error) =>
                bootbox.alert(error)
        });        
    });
}

$(() => {
    // init filters
    if (localStorage.getItem('pageFilters') === null) {
        localStorage.setItem(
                'pageFilters',
                JSON.stringify(buildFiltersObject())
                );
    }

    $("#page-new").click(onCreatePage);

    $("#btn-go-up").click((event) => {
        event.preventDefault();
        event.stopPropagation();

        const target = $(event.target);
        const parentId = $("#filter_parent").val();
        const url = `${target.data('url')}&id=${parentId}`;
        $.ajax({
            method: "get",
            url: url,
            success: function (data) {
                const newId = data.id !== null ?
                        data.id : 0;
                $("select#filter_parent").val(
                        newId.toString()
                        ).change();
                if (newId === 0) {
                    $("#btn-go-up").hide();
                }
            },
            error: (xhr) =>
                alert(xhr.responseText)
        });
    });


    // confirmation on empty trash
    $("a#empty-trash").click((event) => {
        const item = $(event.currentTarget);
        const href = $(item).attr("href");
        event.preventDefault();
        bootbox.confirm(Translation.WannaEmptyTrash, (result) => {
            if (result) {
                location.replace(href);
            }
        });
    });

    // "Show Filters" switch
    $("#show_filters").change((event) => {
        const url = $(event.target).data("url");
        const isChecked = $(event.target).is(':checked');
        if (isChecked) {
            $(".filters").slideDown();
        } else {
            $(".filters").slideUp();
        }

        $.ajax({
            method: "get",
            url: url,
            error: (xhr) =>
                alert(xhr.responseText)
        });
    });

    loadParentPages().then(() => {
        loadFiltersFromlocalStorage();
        bindSelectOnChange();

        const dataTable = $(".tablesorter").DataTable();
        dataTable.ajax.reload();
        dataTable.page(1);
    });
});

const updateGoUpButton = () => {
    const parentId = $("select#filter_parent").val();
    if (parentId && parseInt(parentId) > 0) {
        $("#btn-go-up").show();
    } else {
        $("#btn-go-up").hide();
    }
};

const bindSelectOnChange = () => {
    // fetch updated results after filter values where changed
    $(".filters select").change((event) => {
        const target = event.target;
        const dataTable = $(".tablesorter").DataTable();
        dataTable.ajax.reload();
        dataTable.page(1);

        updateGoUpButton();

        localStorage.setItem(
                'pageFilters',
                JSON.stringify(buildFiltersObject())
                );

        if ($(target).is("#filter_language") ||
                $(target).is("#filter_menu")) {
            loadParentPages();
        }
    });
};

const loadFiltersFromlocalStorage = () => {
    if (localStorage.getItem('pageFilters') === null) {
        return;
    }

    const filters = JSON.parse(localStorage.getItem('pageFilters'));

    $("#filter_type").val(filters.type).trigger("change");
    $("#filter_category").val(filters.category_id).trigger("change");

    const filterParent = $("#filter_parent");
    const parentOptionExists = filterParent.find(`option[value='${filters.parent_id}']`).length;
    const parentId = parentOptionExists ? filters.parent_id : "all";

    $("#filter_approved").val(filters.approved).trigger("change");
    $("#filter_language").val(filters.language).trigger("change");
    $("#filter_menu").val(filters.menu).trigger("change");
    $("#filter_active").val(filters.active).trigger("change");
    $("#filter_parent").val(parentId).trigger("change");

    updateGoUpButton();
};

// filter parent pages by selected language and menu
const loadParentPages = () => {
    const previousParentPage = $("#filter_parent").val();
    const data = {
        csrf_token: $("input[name=csrf_token]")
                .first()
                .val(),
        language: $("#filter_language").val(),
        menu: $("#filter_menu").val()
    };

    const url = $(".filter-wrapper")
            .first()
            .data("parent-pages-url");
    return $.get(url, data, function (text, status) {
        $("#filter_parent").html(text);
        $("#filter_parent").val(previousParentPage).trigger("change");
    });
};