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

    public function getData($start = 0, $length = 10, $draw = 1, $search = null, $view = "default") {
        $result = [];
        $result["data"] = [];

        $columns = [
            "id", "title", "menu", "position", "parent_id", "active", "language", "deleted_at"
        ];

        $user = $this->user;
        $groups = $user->getAllGroups();

        // get the language codes of all groups of the user
        // to filter the page list by it
        $languages = [];
        foreach ($groups as $group) {
            foreach ($group->getLanguages() as $language) {
                $languages[] = "'" . Database::escapeValue($language->getLanguageCode()) . "'";
            }
        }

        // show all deleted or all not deleted pages (recycle bin)
        $where = $view === "default" ? "deleted_at is null" : "deleted_at is not null";

        // filter pages by languages assigned to the user's groups
        if (count($languages)) {
            $where .= " and language in (" . implode(",", $languages) . ")";
        }

        // get total pages count for this user
        $countSql = "select count(id) as count from {prefix}content where $where";
        $countResult = Database::query($countSql, true);
        $countData = Database::fetchObject($countResult);
        $totalCount = $countData->count;

        if ($search) {
            $placeHolderString = "%" . Database::escapeValue(strtolower($search)) . "%";
            $where .= " and lower(title) like '{$placeHolderString}'";
        }

        $where .= " order by menu, position";

        // get filtered pages count
        $countSql = "select count(id) as count from {prefix}content where $where";
        $countResult = Database::query($countSql, true);
        $countData = Database::fetchObject($countResult);
        $filteredCount = $countData->count;

        // query only datasets for the current page
        // to have a good performance
        $where .= " limit $length offset $start";
        $resultsForPage = Database::selectAll("content", $columns, $where);

        $result["data"] = $this->fetchResults($resultsForPage, $user);
        // this is required by DataTables to ensure that always the result of the latest AJAX request is shown
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
    protected function pageDatasetsToResponse($dataset, User $user) {
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
