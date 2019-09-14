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

        $where = "deleted_at is null";


        $results = Database::selectAll("content", [],
                        $where);

        $result["data"] = $this->fetchResults($results);

        $result["draw"] = $draw;

        $result["data"] = array_slice(
                $result["data"],
                $start > 0 ? $start - 1 : 0,
                $length
        );
        $result["recordsFiltered"] = $search ? count($result["data"]) : Database::getNumRows($results);
        $result["recordsTotal"] = Database::getNumRows($results);

        return $result;
    }

    protected function fetchResults($results, $search = null) {
        $filteredResults = [];
        while ($row = Database::fetchObject($results)) {
            $addThis = true;

            if ($search and ! stristr($row->title, $search)) {
                $addThis = false;
            }
            if ($addThis) {
                $filteredResults[] = $this->pageDatasetsToResponse($row);
            }
        }
        return $filteredResults;
    }

    protected
            function pageDatasetsToResponse($dataset) {

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
