<?php

declare(strict_types=1);

namespace UliCMS\CoreContent;

use Database;
use User;
use ArrayHelper;
use Group;
use UliCMS\CoreContent\Partials\ViewButtonRenderer;
use UliCMS\CoreContent\Partials\EditButtonRenderer;
use UliCMS\CoreContent\Partials\DeleteButtonRenderer;

class PageTableRenderer {

    const MODULE_NAME = "core_content";

    public function getData($start = 0, $length = 10, $draw = 1, $search = null) {
        $result = [];
        $result["data"] = [];

        $where = "deleted_at is null";


        $results = Database::selectAll("content", [],
                        $where);

        $user = User::fromSessionData();

        $result["data"] = $this->fetchResults($results, $user, $search);

        $result["draw"] = $draw;

        $result["data"] = array_slice(
                $result["data"],
                $start > 0 ? $start - 1 : 0,
                $length
        );

        $filteredResults = [];
        foreach ($result["data"] as $ds) {
            $addThis = true;
            if ($search and ! stristr($ds["data"][0], $search)) {
                $addThis = false;
            }
            if ($addThis) {
                $filteredResults[] = $ds;
            }
        }
        $result["recordsFiltered"] = count($filteredResults);
        $result["recordsTotal"] = count($result["data"]);

        $result["data"] = $filteredResults;

        return $result;
    }

    protected function fetchResults($results, User $user, ?string $search = null) {
        $filteredResults = [];

        $groups = $user->getAllGroups();

        $languages = [];
        foreach ($groups as $group) {
            foreach ($group->getLanguages() as $language) {
                $languages[] = $language->getLanguageCode();
            }
        }

        while ($row = Database::fetchObject($results)) {
            $addThis = true;

            if (count($languages) and ! in_array($row->language, $languages)) {
                $addThis = false;
            }
            if ($addThis) {
                $filteredResults[] = $this->pageDatasetsToResponse($row);
            }
        }
        return $filteredResults;
    }

    protected function pageDatasetsToResponse($dataset) {

        $viewButtonRenderer = new ViewButtonRenderer();
        $editButtonRenderer = new EditButtonRenderer();
        $deleteButtonRender = new DeleteButtonRenderer();

        $currentUser = User::fromSessionData();
        $id = intval($dataset->id);


        $viewButton = $viewButtonRenderer->render($id, $currentUser);
        $editButton = $editButtonRenderer->render($id, $currentUser);
        $deleteButton = $deleteButtonRender->render($id, $currentUser);

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
            $deleteButton
        ];
    }

}
