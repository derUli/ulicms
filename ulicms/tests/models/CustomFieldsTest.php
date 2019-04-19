<?php

class CustomFieldsTest extends \PHPUnit\Framework\TestCase
{

    public function tearDown()
    {
        $id = $this->getFirstPage()->id;
        $type = $this->getFirstPage()->type;
        Database::pQuery("delete from {prefix}custom_fields where name in (?, ?) and content_id = ?", array(
            "{$type}_foo",
            "{$type}_hello",
            intval($id)
        ), true);
    }

    private function getFirstPage()
    {
        $pages = ContentFactory::getAll();
        return $pages[0];
    }

    public function testSetAndGetField()
    {
        $id = $this->getFirstPage()->id;
        $type = $this->getFirstPage()->type;
        
        CustomFields::set("foo", "bar", $id, true);
        $this->assertEquals("bar", CustomFields::get("foo", $id));
        
        CustomFields::set("hello", "world", $id, true);
        $this->assertEquals("world", CustomFields::get("hello", $id));
        
        $all = CustomFields::getAll($id);
        $this->assertGreaterThanOrEqual(2, count($all));
        
        $this->assertEquals("world", $all["hello"]);
        $this->assertEquals("bar", $all["foo"]);
        
        CustomFields::set("foo", "other_value", $id, true);
        $this->assertEquals("other_value", CustomFields::get("foo", $id));
        
        CustomFields::set("foo", null, $id, true);
        $this->assertNull(CustomFields::get("foo", $id));
        
        CustomFields::set("hello", null, $id, true);
        $this->assertNull(CustomFields::get("hello", $id));
        
        $all = CustomFields::getAll($id);
        $this->assertGreaterThanOrEqual(0, count($all));
    }
}