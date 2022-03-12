<?php

use Spatie\Snapshots\MatchesSnapshots;

class HtmlFieldTest extends \PHPUnit\Framework\TestCase {

    use MatchesSnapshots;

    private $testUser;

    protected function setUp(): void {
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

    protected function tearDown(): void {
        $this->testUser->delete();
    }

    public function testRender() {
        $this->testUser->registerSession();

        $field = new HtmlField();
        $field->name = "my_field";
        $field->title = "content";
        $this->assertMatchesHtmlSnapshot(
                $field->render("hello <strong>world</strong>")
        );
    }

}
