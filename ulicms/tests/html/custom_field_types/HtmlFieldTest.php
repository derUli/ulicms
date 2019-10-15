<?php

class HtmlFieldTest extends \PHPUnit\Framework\TestCase {

    private $testUser;

    public function setUp() {
        include_once getLanguageFilePath("en");

        $user = new User();
        $user->setUsername("testuser-nicht-admin");
        $user->setLastname("Admin");
        $user->setFirstname("Nicht");
        $user->setPassword(uniqid());
        $user->setAdmin(false);
        $user->setHTMLEditor("codemirror");
        $user->save();

        $this->testUser = $user;
    }

    public function tearDown() {
        $this->testUser->delete();
        @session_destroy();
    }

    public function testRender() {

        $this->testUser->registerSession();

        $field = new HtmlField();
        $field->name = "my_field";
        $field->title = "content";
        $rendered = $field->render("hello <strong>world</strong>");

        $expectedFile = Path::resolve("ULICMS_ROOT/tests/fixtures/custom_field_types/html_field.expected.txt");

        // file_put_contents($expectedFile, $rendered);

        $expected = file_get_contents($expectedFile);
        $this->assertEquals($expected, $rendered);
    }

}
