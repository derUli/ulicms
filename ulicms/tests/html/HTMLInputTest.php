<?php

use UliCMS\HTML\Input as Input;
use UliCMS\HTML\ListItem;

class HTMLInputTest extends \PHPUnit\Framework\TestCase {

    public function testTextBox() {
        $this->assertEquals('<input type="text" name="my_field" value="Hello World" required="required">', Input::TextBox("my_field", "Hello World", "text", array(
                    "required" => "required"
        )));
    }

    public function testTextArea() {
        $this->assertEquals('<textarea name="my_field" rows="25" cols="80" required="required">&lt;h2&gt;Hello World!&lt;/h2&gt;</textarea>', Input::TextArea("my_field", "<h2>Hello World!</h2>", 25, 80, array(
                    "required" => "required"
        )));
    }

    public function testPassword() {
        $this->assertEquals('<input type="password" name="my_field" value="Hello World" required="required">', Input::Password("my_field", "Hello World", array(
                    "required" => "required"
        )));
    }

    public function testHidden() {
        $this->assertEquals('<input type="hidden" name="my_field" value="Hello World" required="required">', Input::Hidden("my_field", "Hello World", array(
                    "required" => "required"
        )));
    }

    public function testCheckbox() {
        $this->assertEquals('<input type="checkbox" name="my_field" value="1">', Input::CheckBox("my_field"));
        $this->assertEquals('<input type="checkbox" name="my_field" value="1" checked="checked">', Input::CheckBox("my_field", true));
    }

    public function testRadioButton() {
        $this->assertEquals('<input type="radio" name="my_field" value="1">', Input::RadioButton("my_field"));
        $this->assertEquals('<input type="radio" name="my_field" value="1" checked="checked">', Input::RadioButton("my_field", true));
    }

    public function testListItem() {
        $item = new ListItem("hello_world", "Hello World!");
        $itemSelected = new ListItem("bye_bye", "Bye Bye!", true);

        $this->assertEquals('<option value="hello_world">Hello World!</option>', $item->getHtml());
        $this->assertEquals('<option value="bye_bye" selected>Bye Bye!</option>', $itemSelected->getHtml());
    }

    public function testSingleSelect() {
        $options = array(
            new ListItem("windows", "Windows"),
            new ListItem("linux", "Linux"),
            new ListItem("mac", "macOS")
        );
        $this->assertEquals('<select name="operating_system" size="1"><option value="windows">Windows</option><option value="linux">Linux</option><option value="mac">macOS</option></select>', Input::SingleSelect("operating_system", null, $options));
    }

    public function testSingleSelectWithSelectedItem() {
        $options = array(
            new ListItem("windows", "Windows"),
            new ListItem("linux", "Linux"),
            new ListItem("mac", "macOS")
        );
        $this->assertEquals('<select name="operating_system" size="5"><option value="windows">Windows</option><option value="linux" selected>Linux</option><option value="mac">macOS</option></select>', Input::SingleSelect("operating_system", "linux", $options, 5));
    }

    public function testMultiSelect() {
        $options = array(
            new ListItem("windows", "Windows"),
            new ListItem("linux", "Linux"),
            new ListItem("mac", "macOS")
        );
        $this->assertEquals('<select name="operating_system" size="5" multiple><option value="windows">Windows</option><option value="linux">Linux</option><option value="mac">macOS</option></select>', Input::MultiSelect("operating_system", null, $options));
    }

    public function testMultiSelectWithSelected() {
        $options = array(
            new ListItem("windows", "Windows"),
            new ListItem("linux", "Linux"),
            new ListItem("mac", "macOS")
        );
        $this->assertEquals('<select name="operating_system" size="5" multiple><option value="windows" selected>Windows</option><option value="linux">Linux</option><option value="mac" selected>macOS</option></select>', Input::MultiSelect("operating_system", array(
                    "windows",
                    "mac"
                        ), $options));
    }

    public function testFileWithoutAnything() {
        $this->assertEquals('<input type="file" name="my_file" value="">', Input::File("my_file"));
    }

    public function testFileWithMultiple() {
        $this->assertEquals('<input type="file" name="my_file" value="" multiple="multiple">', Input::File("my_file", true));
    }

    public function testFileWithAcceptAsArray() {
        $this->assertEquals('<input type="file" name="my_file" value="" accept="image/jpeg, image/png">', Input::File("my_file", false, array(
                    "image/jpeg",
                    "image/png"
        )));
    }

    public function testFileWithAcceptAsString() {
        $this->assertEquals('<input type="file" name="my_file" value="" accept="application/pdf">', Input::File("my_file", false, "application/pdf"));
    }

    public function testFileWithAcceptAsStringAndMultiple() {
        $this->assertEquals('<input type="file" name="my_file" value="" accept="application/pdf" multiple="multiple">', Input::File("my_file", true, "application/pdf"));
    }

}
