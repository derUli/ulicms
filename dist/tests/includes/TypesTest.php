<?php

class TypesTest extends \PHPUnit\Framework\TestCase
{
    public function testGetUsedPostTypes()
    {
        $postTypes = get_used_post_types();
        $this->assertContains("page", $postTypes);
    }

    public function testGetAvailablePostTypes()
    {
        $baseTypes = $this->getBaseTypes();
        $availableTypes = get_available_post_types();

        foreach ($baseTypes as $type) {
            $this->assertTrue(in_array($type, $availableTypes));
        }
    }

    private function getBaseTypes()
    {
        $baseTypes = array(
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
}
