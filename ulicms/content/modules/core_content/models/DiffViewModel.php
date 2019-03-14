<?php

namespace UliCMS\CoreContent\Models\ViewModels;

class DiffViewModel {

    public $html;
    public $current_version_date;
    public $old_version_date;
    public $content_id;
    public $history_id;

    public function __construct($html, $current_version_date, $old_version_date, $content_id, $history_id) {
        $this->html = $html;
        $this->current_version_date = $current_version_date;
        $this->old_version_date = $old_version_date;
        $this->content_id = $content_id;
        $this->history_id = $history_id;
    }

}
