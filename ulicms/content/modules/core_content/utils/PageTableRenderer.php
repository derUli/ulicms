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
		
		$columns = [
			"id", "title", "menu", "position", "parent_id", "active", "language"
		];
		
		$user = User::fromSessionData();
		$groups = $user->getAllGroups();

        $languages = [];
        foreach ($groups as $group) {
            foreach ($group->getLanguages() as $language) {
                $languages[] = "'" . Database::escapeValue($language->getLanguageCode()). "'";
            }
        }

        $where = "deleted_at is null";
		
		if(count($languages)){
			$where .= " and language in (".implode(",", $languages).")";
		}
		$where .= " order by menu, position";

        $results = Database::selectAll("content", $columns,
                        $where);
						
		$totalCount = Database::getNumRows($results);
		
		$where .= " limit $start, $length";
		
		$resultsForPage = Database::selectAll("content", $columns,
                        $where);
		
        $result["data"] = $this->fetchResults($resultsForPage, $user);

        $result["recordsTotal"] = $totalCount;
        $filteredResults = $this->filterResults($result["data"], $search, $user);
        $result["data"] = $filteredResults;

        $result["recordsFiltered"] = count($filteredResults) < Database::getNumRows($resultsForPage) ? 
		count($filteredResults) : $totalCount;

        return $result;
    }

    protected function fetchResults($results, User $user) {
        $filteredResults = [];

        while ($row = Database::fetchObject($results)) {
                $filteredResults[] = $this->pageDatasetsToResponse($row, $user);
            }
        
        return $filteredResults;
    }

    protected function filterResults(array $data, ?string $search, User $user) {
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

    protected function pageDatasetsToResponse($dataset, User $user) {
        $viewButtonRenderer = new ViewButtonRenderer();
        $editButtonRenderer = new EditButtonRenderer();
        $deleteButtonRender = new DeleteButtonRenderer();

        $id = intval($dataset->id);

        $viewButton = $viewButtonRenderer->render($id, $user);
        $editButton = $editButtonRenderer->render($id, $user);
        $deleteButton = $deleteButtonRender->render($id, $user);

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
