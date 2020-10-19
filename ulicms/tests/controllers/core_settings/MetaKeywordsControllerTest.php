<?php

class MetaKeywordsControllerTest extends \PHPUnit\Framework\TestCase {

    private $defaultSettings = [];
    
    protected function setUp(): void {
        $this->defaultSettings = [
            "default_language" => Settings::get("default_language"),
            "meta_keywords_de" => Settings::get("meta_keywords_de"),
            "meta_keywords_en" => Settings::get("meta_keywords_en"),
            "meta_keywords" => Settings::get("meta_keywords"),
        ];
    }
    
    protected function tearDown(): void {
        $_POST = [];
        
        foreach($this->defaultSettings as $key => $value){
            Settings::set($key, $value);
        }
    }

    public function testSavePost(): void {
        $_POST["meta_keywords_de"] = "wort1, wort2, wort3";
        $_POST["meta_keywords_en"] = "word1, word2, word3";
        Settings::set("default_language", "en");

        $controller = new MetaKeywordsController();
        $controller->_savePost();

        $this->assertEquals(
                "wort1, wort2, wort3",
                Settings::get('meta_keywords_de')
        );

        $this->assertEquals(
                "word1, word2, word3",
                Settings::get('meta_keywords_en')
        );
         $this->assertEquals(
                "word1, word2, word3",
                Settings::get('meta_keywords')
        );
    }

}
