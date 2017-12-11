<?php
include_once Path::Resolve ( "ULICMS_ROOT/templating.php" );
class ApiTest extends PHPUnit_Framework_TestCase {
	public function testIsCrawler() {
		$useragents = array (
				"Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)" => true,
				"Mozilla/5.0 (compatible; Baiduspider/2.0; +http://www.baidu.com/search/spider.html)" => true,
				"Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.90 Safari/537.36" => false,
				"Mozilla/5.0 (compatible; YandexBot/3.0; +http://yandex.com/bots)" => true,
				"Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; NP08; .NET4.0C; .NET4.0E; NP08; MAAU; rv:11.0) like Gecko" => false,
				"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:55.0) Gecko/20100101 Firefox/55.0" => false 
		);
		foreach ( $useragents as $key => $value ) {
			$this->assertEquals ( $value, is_crawler ( $key ) );
		}
	}
	public function testGetAllUsedLanguages() {
		$languages = getAllUsedLanguages ();
		$this->assertGreaterThanOrEqual ( 2, count ( $languages ) );
		$this->assertTrue ( in_array ( "de", $languages ) );
		$this->assertTrue ( in_array ( "en", $languages ) );
	}
	public function testAddTranslation() {
		$key1 = uniqid ();
		$key2 = "TRANSLATION_" . uniqid ();
		$value1 = uniqid ();
		$value2 = uniqid ();
		$this->assertEquals ( $key1, get_translation ( $key1 ) );
		add_translation ( $key1, $value1 );
		$this->assertEquals ( $value1, get_translation ( $key1 ) );
		add_translation ( $key1, $value2 );
		$this->assertEquals ( $value1, get_translation ( $key1 ) );
		add_translation ( $key2, $value2 );
		$this->assertEquals ( $value2, constant ( strtoupper ( $key2 ) ) );
	}
	public function testGetModuleMeta() {
		$this->assertEquals ( "core", getModuleMeta ( "core_home", "source" ) );
		$meta = getModuleMeta ( "core_home" );
		$this->assertEquals ( "models/HomeViewModel.php", $meta ["objects"] ["HomeViewModel"] );
		$this->assertFalse ( $meta ["embed"] );
		$this->assertNull ( getModuleMeta ( "not_a_module" ) );
		$this->assertNull ( getModuleMeta ( "not_a_module", "version" ) );
		$this->assertNull ( getModuleMeta ( "core_home", "not_here" ) );
	}
	public function testBool2YesNo() {
		$this->assertEquals ( get_translation ( "yes" ), bool2YesNo ( 1 ) );
		$this->assertEquals ( get_translation ( "no" ), bool2YesNo ( 0 ) );
		$this->assertEquals ( get_translation ( "yes" ), bool2YesNo ( true ) );
		$this->assertEquals ( get_translation ( "no" ), bool2YesNo ( false ) );
		
		$this->assertEquals ( "cool", bool2YesNo ( 1, "cool", "doof" ) );
		$this->assertEquals ( "doof", bool2YesNo ( 0, "cool", "doof" ) );
		$this->assertEquals ( "cool", bool2YesNo ( true, "cool", "doof" ) );
		$this->assertEquals ( "doof", bool2YesNo ( false, "cool", "doof" ) );
	}
	public function testGetMime() {
		$this->assertEquals ( "text/plain", get_mime ( Path::resolve ( "ULICMS_ROOT/.htaccess" ) ) );
		$this->assertEquals ( "image/gif", get_mime ( Path::resolve ( "ULICMS_ROOT/admin/gfx/edit.gif" ) ) );
		$this->assertEquals ( "image/png", get_mime ( Path::resolve ( "ULICMS_ROOT/admin/gfx/edit.png" ) ) );
	}
}
