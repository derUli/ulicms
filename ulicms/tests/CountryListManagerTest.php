<?php
class CountryListManagerTest extends PHPUnit_Framework_TestCase {
	public function testLoadByContinentName() {
		$manager = new CountryManager ();
		$this->assertEquals ( 53, count ( $manager->getAllByContinentName ( "Europe" ) ) );
		$this->assertEquals ( 52, count ( $manager->getAllByContinentName ( "Asia" ) ) );
	}
	public function testLoadByContinent() {
		$manager = new CountryManager ();
		$this->assertEquals ( 53, count ( $manager->getAllByContinent ( "EU" ) ) );
		$this->assertEquals ( 52, count ( $manager->getAllByContinent ( "AS" ) ) );
	}
	public function testLoadByCurrency() {
		$manager = new CountryManager ();
		$this->assertEquals ( 35, count ( $manager->getAllByCurrencyCode ( "EUR" ) ) );
		$this->assertEquals ( 17, count ( $manager->getAllByCurrencyCode ( "USD" ) ) );
	}
	public function testLoadAll() {
		$manager = new CountryManager ();
		$this->assertEquals ( 250, count ( $manager->getAll () ) );
	}
}
