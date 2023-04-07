<?php

use App\HTML\Input as Input;
use App\HTML\ListItem;

class ListItemTest extends \PHPUnit\Framework\TestCase
{
    public function testGetHtml()
    {
        $item = new ListItem("hello_world", "Hello World!");
        $itemSelected = new ListItem("bye_bye", "Bye Bye!", true);

        $this->assertEquals(
            '<option value="hello_world">Hello World!</option>',
            $item->getHtml()
        );
        $this->assertEquals(
            '<option value="bye_bye" selected>Bye Bye!</option>',
            $itemSelected->getHtml()
        );
    }

    public function testRender()
    {
        $item = new ListItem("hello_world", "Hello World!");
        $itemSelected = new ListItem("bye_bye", "Bye Bye!", true);

        ob_start();
        $item->render();
        $this->assertEquals(
            '<option value="hello_world">Hello World!</option>',
            ob_get_clean()
        );

        ob_start();
        $itemSelected->render();
        $this->assertEquals(
            '<option value="bye_bye" selected>Bye Bye!</option>',
            ob_get_clean()
        );
    }

    public function testToString()
    {
        $item = new ListItem("hello_world", "Hello World!");

        $this->assertEquals(
            '<option value="hello_world">Hello World!</option>',
            (string)$item
        );
    }

    public function testGetSelectedReturnsTrue()
    {
        $item = new ListItem("bye_bye", "Bye Bye!", true);
        $this->assertTrue($item->getSelected());
    }

    public function testGetSelectedReturnsFalse()
    {
        $item = new ListItem("bye_bye", "Bye Bye!", false);
        $this->assertFalse($item->getSelected());
    }

    public function testSetAndGetText()
    {
        $item = new ListItem("hello_world", "Hello World!");
        $item->setText("Bye Bye");

        $this->assertEquals("Bye Bye", $item->getText());
    }

    public function testSetAndGetValue()
    {
        $item = new ListItem("hello_world", "Hello World!");
        $item->setValue("bye_bye");

        $this->assertEquals("bye_bye", $item->getValue());
    }

    public function testSetSelected()
    {
        $item = new ListItem("bye_bye", "Bye Bye!", false);
        $item->setSelected(true);

        $this->assertTrue($item->getSelected());
    }
}
