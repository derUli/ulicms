<?php
class PageTest extends PHPUnit_Framework_TestCase {
	private $ipsum = 'Lorem ipsum dolor sit amet,
		[module="fortune2"]
		[module="test"]
		[module=&quot;hello&quot;]
		consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.';
	public function testGetEmbeddedModulesPage() {
		$page = new Page ();
		$page->content = $this->ipsum;
		$this->assertEquals ( 3, count ( $page->getEmbeddedModules () ) );
	}
	public function testGetEmbeddedModulesModulePage() {
		$page = new Module_Page ();
		
		$page->content = $this->ipsum;
		$this->assertEquals ( 3, count ( $page->getEmbeddedModules () ) );
		$page->module = "fortune2";
		$this->assertEquals ( 3, count ( $page->getEmbeddedModules () ) );
		$page->module = "pfbc_sample";
		$this->assertEquals ( 4, count ( $page->getEmbeddedModules () ) );
	}
} 