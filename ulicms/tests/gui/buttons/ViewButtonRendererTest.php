<?php

use UliCMS\CoreContent\Partials\ViewButtonRenderer;

class ViewButtonRendererTest extends \PHPUnit\Framework\TestCase {

	private $user;

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
		$allGroups = Group::getAll();

		$page = new Page();
		$page->slug = uniqid();
		$page->title = "Test Page " . uniqid();
		$page->author_id = $this->user->getId();
		$page->group_id = $allGroups[0]->getId();
		$page->save();

		$render = new ViewButtonRenderer();

		$html = $render->render($page->getID(),
				$this->user);

		$this->assertStringContainsString(
				'<i class="fa fa-eye', $html
		);
		$this->assertStringContainsString(
				'?goid=', $html
		);
	}

	public function testRenderNonRegularReturnsNothing() {

		$allGroups = Group::getAll();

		$page = new Node();
		$page->slug = uniqid();
		$page->title = "Test Page " . uniqid();
		$page->author_id = $this->user->getId();
		$page->group_id = $allGroups[0]->getId();
		$page->save();

		$render = new ViewButtonRenderer();

		$this->assertEmpty($render->render($page->getID(),
						$this->user));
	}

	public function testRenderCanNotReadReturnsNothing() {

		$allGroups = Group::getAll();

		$page = new Page();
		$page->slug = uniqid();
		$page->title = "Test Page " . uniqid();
		$page->author_id = $this->user->getId();
		$page->group_id = $allGroups[0]->getId();
		$page->access = strval(PHP_INT_MAX);
		$page->save();

		$render = new ViewButtonRenderer();

		$this->assertEmpty($render->render($page->getID(),
						$this->user));
	}

}
