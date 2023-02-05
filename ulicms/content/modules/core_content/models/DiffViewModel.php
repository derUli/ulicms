<?php

declare(strict_types=1);

namespace UliCMS\CoreContent\Models\ViewModels;

class DiffViewModel {

    public $html;
    public $current_version_date;
    public $old_version_date;
    public $content_id;
    public $history_id;

    public function __construct(
            string $html,
            string $current_version_date,
            string $old_version_date,
            int $content_id,
            int $history_id
    ) {
        $this->html = $html;
        $this->current_version_date = $current_version_date;
        $this->old_version_date = $old_version_date;
        $this->content_id = $content_id;
        $this->history_id = $history_id;
    }

}
