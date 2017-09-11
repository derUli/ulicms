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
	public function testGetAllUsedLanguages(){
		$languages = getAllUsedLanguages();
		$this->assertEquals(2, count($languages));
		$this->assertTrue(in_array("de", $languages));
		$this->assertTrue(in_array("en", $languages));
	}
}
