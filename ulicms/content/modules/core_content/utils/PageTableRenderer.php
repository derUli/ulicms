<?php

declare(strict_types=1);

namespace UliCMS\CoreContent;

use Database;
use User;
use UliCMS\CoreContent\Partials\ViewButtonRenderer;
use UliCMS\CoreContent\Partials\EditButtonRenderer;
use UliCMS\CoreContent\Partials\DeleteButtonRenderer;
use UliCMS\CoreContent\Partials\UnDeleteButtonRenderer;

class PageTableRenderer {

    private $user;

    public function __construct($user = null) {
        $this->user = !$user ? User::fromSessionData() : $user;
    }

    const MODULE_NAME = "core_content";

    // get paginated data for DataTables
    // returns an array with can be returned to client
    // as json
    // $start - first item index of pagination
    // $length - count of items shown at the page
    // $draw - this is sent by DataTables and must be returned
    // It's used by DataTables to ensure that always the latest data is shown
    // $search - A search subject. If set the results are filtered by it
    // $view show all not deleted pages or the recyle bin
    // $order = order array with sorcolumn, and dir (direction)
    // returns an array of results
    public function getData(
            int $start = 0,
            int $length = 10,
            int $draw = 1,
            ?string $search = null,
            string $view = "default",
            ?array $order = null
    ): array {
        $result = [];
        $result["data"] = [];

        $columns = [
            "id",
            "title",
            "menu",
            "position",
            "parent_id",
            "active",
            "language",
            "deleted_at"
        ];

        $orderColumns = [
            "title",
            "menu",
            "position",
            "parent_id",
            "active"
        ];

        $sortColumn = "position";
        $sortDirection = "asc";

        if ($order) {
            $sortDirection = (isset($order["dir"]) and $order["dir"] === "desc") ? "desc" : "asc";
            $columnNumber = isset($order["column"]) ? intval($order["column"]) : 0;
            if ($columnNumber >= 0 and $columnNumber < count($orderColumns)) {
                $sortColumn = $orderColumns[$columnNumber];
            }
        }

        $user = $this->user;
        $groups = $user->getAllGroups();

        // get the language codes of all groups of the user
        // to filter the page list by it
        $languages = [];
        foreach ($groups as $group) {
            foreach ($group->getLanguages() as $language) {
                $languages[] = "'" .
                        Database::escapeValue($language->getLanguageCode())
                        . "'";
            }
        }

        // show all deleted or all not deleted pages (recycle bin)
        $where = $view === "default" ?
                "deleted_at is null" : "deleted_at is not null";

        // filter pages by languages assigned to the user's groups
        if (count($languages)) {
            $where .= " and language in (" . implode(",", $languages) . ")";
        }

        // get total pages count for this user
        $countSql = "select count(id) as count from {prefix}content "
                . "where $where";
        $countResult = Database::query($countSql, true);
        $countData = Database::fetchObject($countResult);
        $totalCount = $countData->count;

        if ($search) {
            $placeHolderString = "%" . Database::escapeValue(
                            strtolower($search)
                    ) . "%";
            $where .= " and lower(title) like '{$placeHolderString}'";
        }

        $where .= " order by $sortColumn $sortDirection";

        // get filtered pages count
        $countSql = "select count(id) as count from {prefix}content "
                . "where $where";
        $countResult = Database::query($countSql, true);
        $countData = Database::fetchObject($countResult);
        $filteredCount = $countData->count;

        // query only datasets for the current page
        // to have a good performance
        $where .= " limit $length offset $start";
        $resultsForPage = Database::selectAll("content", $columns, $where);

        $result["data"] = $this->fetchResults($resultsForPage, $user);
        // this is required by DataTables to ensure that always the result
        // of the latest AJAX request is shown
        $result["draw"] = $draw;

        // Total count of pages shown to the user
        $result["recordsTotal"] = $totalCount;

        // Filtered page count if the user apply filters, else total page count
        $result["recordsFiltered"] = $search ? $filteredCount : $totalCount;

        return $result;
    }

    // fetch all datasets of mysqli result
    protected function fetchResults(\mysqli_result $results, User $user) {
        $filteredResults = [];

        while ($row = Database::fetchObject($results)) {
            $filteredResults[] = $this->pageDatasetsToResponse($row, $user);
        }

        return $filteredResults;
    }

    // builds an array which is used to show table data in frontend
    protected function pageDatasetsToResponse(object $dataset, User $user) {
        $viewButtonRenderer = new ViewButtonRenderer();
        $editButtonRenderer = new EditButtonRenderer();

        $deleteButtonRenderer = new DeleteButtonRenderer();
        $undeleteButtonRenderer = new UnDeleteButtonRenderer();

        // render button iconssuch as view, edit and delete
        $id = intval($dataset->id);
        $viewButton = $viewButtonRenderer->render($id, $user);
        $editButton = $editButtonRenderer->render($id, $user);
        $deleteButton = $deleteButtonRenderer->render($id, $user);
        $undeleteButton = $undeleteButtonRenderer->render($id, $user);

        return [
            _esc($dataset->title),
            _esc(get_translation($dataset->menu)),
            _esc($dataset->position),
            _esc(getPageTitleByID(
                            intval(
                                    $dataset->parent_id)
                    )
            ),
            bool2YesNo(
                    boolval(
                            $dataset->active
                    )
            ),
            $viewButton,
            $editButton,
            !$dataset->deleted_at ? $deleteButton : $undeleteButton
        ];
    }

}