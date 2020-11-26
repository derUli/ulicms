<?php

class DefaultAccessRestrictionsControllerTest extends \PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        $_POST = [];
        Settings::set(
            "only_admins_can_edit",
            0
        );
        Settings::set(
            "only_group_can_edit",
            0
        );
        Settings::set(
            "only_owner_can_edit",
            0
        );
        Settings::set(
            "only_others_can_edit",
            0
        );
    }

    public function testSavePost()
    {
        $_POST = [
            "only_admins_can_edit" => "1",
            "only_owner_can_edit" => "1",
        ];

        $controller = new DefaultAccessRestrictionsController();
        $controller->_savePost();

        $this->assertEquals(1, Settings::get("only_admins_can_edit", "int"));
        $this->assertEquals(1, Settings::get("only_owner_can_edit", "int"));

        $this->assertEquals(0, Settings::get("only_group_can_edit", "int"));
        $this->assertEquals(0, Settings::get("only_others_can_edit", "int"));
    }
}
