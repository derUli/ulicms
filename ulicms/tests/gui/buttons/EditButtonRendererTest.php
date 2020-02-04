<?php

use UliCMS\CoreContent\Partials\EditButtonRenderer;

class EditButtonRendererTest extends \PHPUnit\Framework\TestCase {

    private $user;
    private $group;

    public function setUp() {
        $user = new User();
        $user->setUsername("paul.panzer");
        $user->setLastname("Panzer");
        $user->setFirstname("Paul");
        $user->setPassword("secret");
        $user->setEmail("paul@panzer.de");
        $user->save();

        $this->user = $user;
    }

    public function tearDown() {
        $this->user->delete();

        Database::query("delete from {prefix}content where title like 'Test Page%'", true);
    }

    public function testRenderReturnsHtml() {

        $this->user->setAdmin(true);
        $this->user->save();

        $allGroups = Group::getAll();

        $page = new Page();
        $page->slug = uniqid();
        $page->title = "Test Page " . uniqid();
        $page->author_id = $this->user->getId();
        $page->group_id = $allGroups[0]->getId();
        $page->save();

        $render = new EditButtonRenderer();

        $html = $render->render($page->getID(),
                $this->user);

        $this->assertStringContainsString(
                '<i class="fas fa-pencil-alt', $html
        );
        $this->assertStringContainsString(
                '?action=pages_edit&amp;page=', $html
        );
    }

    public function testRenderReturnsNothing() {
        $allGroups = Group::getAll();

        $this->user->setAdmin(false);
        $this->user->save();

        $page = new Page();
        $page->slug = uniqid();
        $page->title = "Test Page " . uniqid();
        $page->author_id = $this->user->getId();
        $page->group_id = $allGroups[0]->getId();
        $page->save();

        $render = new EditButtonRenderer();

        $this->assertEmpty($render->render($page->getID(),
                        $this->user));
    }

}
