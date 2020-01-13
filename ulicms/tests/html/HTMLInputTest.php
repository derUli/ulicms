<?php

use UliCMS\HTML\Input as Input;
use UliCMS\HTML\ListItem;

class HTMLInputTest extends \PHPUnit\Framework\TestCase {

    public function testTextBox() {
        $this->assertEquals('<input type="text" name="my_field" value="Hello World" required="required">', Input::textBox("my_field", "Hello World", "text", array(
                    "required" => "required"
        )));
    }

    public function testTextArea() {
        $this->assertEquals('<textarea name="my_field" rows="25" cols="80" required="required">&lt;h2&gt;Hello World!&lt;/h2&gt;</textarea>', Input::textArea("my_field", "<h2>Hello World!</h2>", 25, 80, array(
                    "required" => "required"
        )));
    }

    public function testPassword() {
        $this->assertEquals('<input type="password" name="my_field" value="Hello World" required="required">', Input::password("my_field", "Hello World", array(
                    "required" => "required"
        )));
    }

    public function testHidden() {
        $this->assertEquals('<input type="hidden" name="my_field" value="Hello World" required="required">', Input::hidden("my_field", "Hello World", array(
                    "required" => "required"
        )));
    }

    public function testCheckbox() {
        $this->assertEquals('<input type="checkbox" name="my_field" value="1">', Input::checkBox("my_field"));
        $this->assertEquals('<input type="checkbox" name="my_field" value="1" checked="checked">', Input::checkBox("my_field", true));
    }

    public function testRadioButton() {
        $this->assertEquals('<input type="radio" name="my_field" value="1">', Input::radioButton("my_field"));
        $this->assertEquals('<input type="radio" name="my_field" value="1" checked="checked">', Input::radioButton("my_field", true));
    }

    public function testSingleSelect() {
        $options = array(
            new ListItem("windows", "Windows"),
            new ListItem("linux", "Linux"),
            new ListItem("mac", "macOS")
        );
        $this->assertEquals(
                '<select name="operating_system" size="1" class="my-class"><option value="windows">Windows</option><option value="linux">Linux</option><option value="mac">macOS</option></select>',
                Input::singleSelect(
                        "operating_system",
                        null,
                        $options,
                        1,
                        [
                            "class" => "my-class"
                        ]
                )
        );
    }

    public function testSingleSelectWithSelectedItem() {
        $options = array(
            new ListItem("windows", "Windows"),
            new ListItem("linux", "Linux"),
            new ListItem("mac", "macOS")
        );
        $this->assertEquals('<select name="operating_system" size="5"><option value="windows">Windows</option><option value="linux" selected>Linux</option><option value="mac">macOS</option></select>', Input::singleSelect("operating_system", "linux", $options, 5));
    }

    public function testMultiSelect() {
        $options = array(
            new ListItem("windows", "Windows"),
            new ListItem("linux", "Linux"),
            new ListItem("mac", "macOS")
        );
        $this->assertEquals('<select name="operating_system" size="5" multiple><option value="windows">Windows</option><option value="linux">Linux</option><option value="mac">macOS</option></select>', Input::multiSelect("operating_system", null, $options));
    }

    public function testMultiSelectWithSelected() {
        $options = array(
            new ListItem("windows", "Windows"),
            new ListItem("linux", "Linux"),
            new ListItem("mac", "macOS")
        );
        $this->assertEquals(
                '<select name="operating_system" size="4" class="foo" multiple><option value="windows" selected>Windows</option><option value="linux">Linux</option><option value="mac" selected>macOS</option></select>',
                Input::multiSelect("operating_system",
                        [
                            "windows",
                            "mac"
                        ],
                        $options,
                        4,
                        [
                            "class" => "foo"
                        ]
                )
        );
    }

    public function testFileWithoutAnything() {
        $this->assertEquals('<input type="file" name="my_file" value="">', Input::file("my_file"));
    }

    public function testFileWithHtmlAttributes() {
        $this->assertEquals('<input type="file" name="my_file" value="" class="foo">',
                Input::file(
                        "my_file",
                        false,
                        null,
                        [
                            "class" => "foo"
                        ]
                )
        );
    }

    public function testFileWithMultiple() {
        $this->assertEquals('<input type="file" name="my_file" value="" multiple="multiple">', Input::file("my_file", true));
    }

    public function testFileWithAcceptAsArray() {
        $this->assertEquals('<input type="file" name="my_file" value="" accept="image/jpeg, image/png">', Input::file("my_file", false, array(
                    "image/jpeg",
                    "image/png"
        )));
    }

    public function testFileWithAcceptAsString() {
        $this->assertEquals('<input type="file" name="my_file" value="" accept="application/pdf">', Input::file("my_file", false, "application/pdf"));
    }

    public function testFileWithAcceptAsStringAndMultiple() {
        $this->assertEquals('<input type="file" name="my_file" value="" accept="application/pdf" multiple="multiple">', Input::file("my_file", true, "application/pdf"));
    }

    public function testEditorWithoutClass() {
        $this->assertEquals(
                '<textarea name="foo" rows="20" cols="70" id="foo" ' .
                'class="ckeditor" data-mimetype="text/html">' .
                '&lt;strong&gt;bar&lt;/strong</textarea>',
                Input::editor(
                        "foo",
                        '<strong>bar</strong',
                        20, 70,
                        [
                        ]
                )
        );
    }

    public function testEditorWithClass() {
        $this->assertEquals(
                '<textarea name="foo" rows="20" cols="70" ' .
                'class="foo-class ckeditor" id="foo" ' .
                'data-mimetype="text/html">' .
                '&lt;strong&gt;bar&lt;/strong</textarea>',
                Input::editor(
                        "foo",
                        '<strong>bar</strong',
                        20, 70,
                        [
                            "class" => "foo-class"
                        ]
                )
        );
    }
    
    public function testGetCKEditorSkins(){
        $skins = Input::getCKEditorSkins();
        
        $this->assertGreaterThanOrEqual(1, count($skins));
        $this->assertContains("moono", $skins);
    }

}
