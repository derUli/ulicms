<?php

namespace UliCMS\Models\Content\Types;

class ContentType {

    public $show = [];
    public $customFieldTabTitle;
    public $customFields = [];

    public function toJSON() {
        return json_encode(array(
            "show" => $this->show
                ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

}
