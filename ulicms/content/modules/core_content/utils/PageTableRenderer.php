<?php

declare(strict_types=1);

namespace UliCMS\CoreContent;

use Database;
use User;
use UliCMS\CoreContent\Partials\ViewButtonRenderer;
use UliCMS\CoreContent\Partials\EditButtonRenderer;
use UliCMS\CoreContent\Partials\DeleteButtonRenderer;

class PageTableRenderer {

    const MODULE_NAME = "core_content";

    public function getData($start = 0, $length = 10, $draw = 1, $search = null) {
        $result = [];
        $result["data"] = [];

        $where = "deleted_at is null order by menu, position";

        $results = Database::selectAll("content", [],
                        $where);

        $user = User::fromSessionData();

        $result["data"] = $this->fetchResults($results, $user, $search);

        $result["draw"] = $draw;

        $result["recordsTotal"] = count($result["data"]);
        $filteredResults = $this->filterResults($result["data"], $search);
        $result["data"] = $filteredResults;

        $result["recordsFiltered"] = count($filteredResults);

        $result["data"] = array_slice(
                $result["data"],
                $start > 0 ? $start - 1 : 0,
                $length
        );

        return $result;
    }

    protected function fetchResults($results, User $user) {
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

    protected function filterResults(array $data, ?string $search) {
        $filteredResults = [];
        foreach ($data as $ds) {
            $addThis = true;
            if ($search and ! stristr($ds[0], $search)) {
                $addThis = false;
            }
            if ($addThis) {
                $filteredResults[] = $ds;
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
