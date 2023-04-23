<?php

use App\Models\Content\Types\ContentType;
use App\Models\Content\Types\DefaultContentTypes;

class ContentTypesTest extends \PHPUnit\Framework\TestCase {
    public function testTypesArray(): void {
        DefaultContentTypes::initTypes();
        $types = DefaultContentTypes::getAll();
        $this->assertTrue(is_array($types));
        $this->assertGreaterThanOrEqual(11, $types);

        $baseTypes = $this->getBaseTypes();

        foreach ($baseTypes as $type) {
            $this->assertArrayHasKey($type, $types);
        }

        foreach ($types as $type) {
            $this->assertInstanceOf(ContentType::class, $type);
            $this->assertTrue(is_array($type->show));
        }
    }

    public function testIsRegularReturnsTrue(): void {
        $types = ['Page', 'Article', 'Image_Page',
            'Video_Page', 'Audio_Page', 'Snippet',
            'Module_Page', 'Content_List'];
        foreach ($types as $type) {
            $model = new $type();
            $this->assertTrue($model->isRegular());
        }
    }

    public function testIsRegularReturnsFalse(): void {
        $types = ['Link', 'Node', 'Language_Link'];
        foreach ($types as $type) {
            $model = new $type();
            $this->assertFalse($model->isRegular());
        }
    }

       private function getBaseTypes() {
           $baseTypes = [
               'page',
               'article',
               'snippet',
               'list',
               'link',
               'language_link',
               'node',
               'image',
               'module',
               'video',
               'audio'
           ];
           return $baseTypes;
       }
}
