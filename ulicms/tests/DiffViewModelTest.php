<?php

use UliCMS\CoreContent\Models\ViewModels\DiffViewModel;

class DiffViewModelTest extends \PHPUnit\Framework\TestCase {

    public function testConstructor() {
        $model = new DiffViewModel("Foo<strong>Bar</strong>",
                "2019-03-13", "2019-01-17", 123, 666);
        $this->assertEquals("Foo<strong>Bar</strong>", $model->html);
        $this->assertEquals("2019-03-13", $model->current_version_date);
        $this->assertEquals("2019-01-17", $model->old_version_date);
        $this->assertEquals(123, $model->content_id);
        $this->assertEquals(666, $model->history_id);
    }

}
