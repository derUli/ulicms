<?php
class CountryListTest extends PHPUnit_Framework_TestCase {
	public function testLoadByCountryCode() {
		$country = new Country ();
		$country->loadByCountryCode ( "KP" );
		$this->assertEquals ( 121, $country->getId () );
		$this->assertEquals ( "KP", $country->getCountryCode () );
		$this->assertEquals ( "North Korea", $country->getCountryName () );
		$this->assertEquals ( "KN", $country->getFipsCode () );
		$this->assertEquals ( "408", $country->getIsoNumeric () );
		$this->assertEquals ( 43.006054, $country->getNorth () );
		$this->assertEquals ( 37.673332, $country->getSouth () );
		$this->assertEquals ( 130.674866, $country->getEast () );
		$this->assertEquals ( 124.315887, $country->getWest () );
		$this->assertEquals ( "Pyongyang", $country->getCapital () );
		$this->assertEquals ( "Asia", $country->getContinentName () );
		$this->assertEquals ( "AS", $country->getContinent () );
		$this->assertEquals ( "ko-KP", $country->getLanguages () );
		$this->assertEquals ( "PRK", $country->getIsoAlpha3 () );
		$this->assertEquals ( 1873107, $country->getGeonameId () );
	}
	public function testLoadByName() {
		$country = new Country ();
		$country->loadByCountryName ( "North Korea" );
		$this->assertEquals ( 121, $country->getId () );
		$this->assertEquals ( "KP", $country->getCountryCode () );
		$this->assertEquals ( "North Korea", $country->getCountryName () );
		$this->assertEquals ( "KN", $country->getFipsCode () );
		$this->assertEquals ( "408", $country->getIsoNumeric () );
		$this->assertEquals ( 43.006054, $country->getNorth () );
		$this->assertEquals ( 37.673332, $country->getSouth () );
		$this->assertEquals ( 130.674866, $country->getEast () );
		$this->assertEquals ( 124.315887, $country->getWest () );
		$this->assertEquals ( "Pyongyang", $country->getCapital () );
		$this->assertEquals ( "Asia", $country->getContinentName () );
		$this->assertEquals ( "AS", $country->getContinent () );
		$this->assertEquals ( "ko-KP", $country->getLanguages () );
		$this->assertEquals ( "PRK", $country->getIsoAlpha3 () );
		$this->assertEquals ( 1873107, $country->getGeonameId () );
	}
	public function testLoadById() {
		$country = new Country ( 121 );
		$this->assertNotNull ( $country->getId () );
		$this->assertEquals ( "KP", $country->getCountryCode () );
		$this->assertEquals ( "North Korea", $country->getCountryName () );
		$this->assertEquals ( "KN", $country->getFipsCode () );
		$this->assertEquals ( "408", $country->getIsoNumeric () );
		$this->assertEquals ( 43.006054, $country->getNorth () );
		$this->assertEquals ( 37.673332, $country->getSouth () );
		$this->assertEquals ( 130.674866, $country->getEast () );
		$this->assertEquals ( 124.315887, $country->getWest () );
		$this->assertEquals ( "Pyongyang", $country->getCapital () );
		$this->assertEquals ( "Asia", $country->getContinentName () );
		$this->assertEquals ( "AS", $country->getContinent () );
		$this->assertEquals ( "ko-KP", $country->getLanguages () );
		$this->assertEquals ( "PRK", $country->getIsoAlpha3 () );
		$this->assertEquals ( 1873107, $country->getGeonameId () );
	}
}
