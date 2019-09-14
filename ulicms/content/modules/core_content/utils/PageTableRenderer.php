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

    const MODULE_NAME = "core_content";

    public function getData($start = 0, $length = 10, $draw = 1, $search = null, $view = "default") {
        $result = [];
        $result["data"] = [];

        $columns = [
            "id", "title", "menu", "position", "parent_id", "active", "language", "deleted_at"
        ];

        $user = User::fromSessionData();
        $groups = $user->getAllGroups();

        $languages = [];
        foreach ($groups as $group) {
            foreach ($group->getLanguages() as $language) {
                $languages[] = "'" . Database::escapeValue($language->getLanguageCode()) . "'";
            }
        }

        $where = $view === "default" ? "deleted_at is null" : "deleted_at is not null";

        if (count($languages)) {
            $where .= " and language in (" . implode(",", $languages) . ")";
        }

        $countSql = "select count(id) as count from {prefix}content where $where";
        $countResult = Database::query($countSql, true);
        $countData = Database::fetchObject($countResult);
        $totalCount = $countData->count;

        if ($search) {
            $placeHolderString = "%" . Database::escapeValue(strtolower($search)) . "%";
            $where .= " and lower(title) like '{$placeHolderString}'";
        }

        $where .= " order by menu, position";

        $countSql = "select count(id) as count from {prefix}content where $where";
        $countResult = Database::query($countSql, true);
        $countData = Database::fetchObject($countResult);
        $filteredCount = $countData->count;

        $where .= " limit $start, $length";

        $resultsForPage = Database::selectAll("content", $columns, $where);

        $result["data"] = $this->fetchResults($resultsForPage, $user);
        $result["draw"] = $draw;

        $result["recordsTotal"] = $totalCount;

        $result["recordsFiltered"] = $search ? $filteredCount : $totalCount;

        return $result;
    }

    protected function fetchResults($results, User $user) {
        $filteredResults = [];

        while ($row = Database::fetchObject($results)) {
            $filteredResults[] = $this->pageDatasetsToResponse($row, $user);
        }

        return $filteredResults;
    }

    protected function pageDatasetsToResponse($dataset, User $user) {
        $viewButtonRenderer = new ViewButtonRenderer();
        $editButtonRenderer = new EditButtonRenderer();

        $deleteButtonRenderer = new DeleteButtonRenderer();
        $undeleteButtonRenderer = new UnDeleteButtonRenderer();

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
