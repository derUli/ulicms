<?php
class ContentTypesTest extends PHPUnit_Framework_TestCase {
	private function getBaseTypes() {
		$baseTypes = array (
				"page",
				"article",
				"snippet",
				"list",
				"link",
				"language_link",
				"node",
				"image",
				"module",
				"video",
				"audio" 
		);
		return $baseTypes;
	}
	public function testTypesArray() {
		DefaultContentTypes::initTypes ();
		$types = DefaultContentTypes::getAll ();
		$this->assertTrue ( is_array ( $types ) );
		$this->assertGreaterThanOrEqual ( 11, $types );
		
		$baseTypes = $this->getBaseTypes ();
		
		foreach ( $baseTypes as $type ) {
			$this->assertArrayHasKey ( $type, $types );
		}
		
		foreach ( $types as $type ) {
			$this->assertInstanceOf ( ContentType::class, $type );
			$this->assertTrue ( is_array ( $type->show ) );
		}
	}
	public function testGetAvailablePostTypes() {
		$baseTypes = $this->getBaseTypes ();
		$availableTypes = get_available_post_types ();
		
		foreach ( $baseTypes as $type ) {
			$this->assertTrue ( in_array ( $type, $availableTypes ) );
		}
	}
}