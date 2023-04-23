<?php

use App\HTML\ListItem;

class ListItemTest extends \PHPUnit\Framework\TestCase {
    public function testGetHtml(): void {
        $item = new ListItem('hello_world', 'Hello World!');
        $itemSelected = new ListItem('bye_bye', 'Bye Bye!', true);

        $this->assertEquals(
            '<option value="hello_world">Hello World!</option>',
            $item->getHtml()
        );
        $this->assertEquals(
            '<option value="bye_bye" selected>Bye Bye!</option>',
            $itemSelected->getHtml()
        );
    }

    public function testRender(): void {
        $item = new ListItem('hello_world', 'Hello World!');
        $itemSelected = new ListItem('bye_bye', 'Bye Bye!', true);

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

    public function testToString(): void {
        $item = new ListItem('hello_world', 'Hello World!');

        $this->assertEquals(
            '<option value="hello_world">Hello World!</option>',
            (string)$item
        );
    }

    public function testGetSelectedReturnsTrue(): void {
        $item = new ListItem('bye_bye', 'Bye Bye!', true);
        $this->assertTrue($item->getSelected());
    }

    public function testGetSelectedReturnsFalse(): void {
        $item = new ListItem('bye_bye', 'Bye Bye!', false);
        $this->assertFalse($item->getSelected());
    }

    public function testSetAndGetText(): void {
        $item = new ListItem('hello_world', 'Hello World!');
        $item->setText('Bye Bye');

        $this->assertEquals('Bye Bye', $item->getText());
    }

    public function testSetAndGetValue(): void {
        $item = new ListItem('hello_world', 'Hello World!');
        $item->setValue('bye_bye');

        $this->assertEquals('bye_bye', $item->getValue());
    }

    public function testSetSelected(): void {
        $item = new ListItem('bye_bye', 'Bye Bye!', false);
        $item->setSelected(true);

        $this->assertTrue($item->getSelected());
    }
}
