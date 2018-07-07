<?php

class CustomFieldsTest extends PHPUnit_Framework_TestCase
{

    public function tearDown()
    {
        $id = $this->getFirstPage()->id;
        Database::pQuery("delete from {prefix}custom_fields where name in (?, ?) and content_id = ?", array(
            "foo",
            "hello",
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
        
        CustomFields::set("foo", "bar", $id);
        $this->assertEquals("bar", CustomFields::get("foo", $id));
        
        CustomFields::set("hello", "world", $id);
        $this->assertEquals("world", CustomFields::get("hello", $id));
        
        $all = CustomFields::getAll($id);
        $this->assertGreaterThanOrEqual(2, count($all));
        
        $this->assertEquals("world", $all["hello"]);
        $this->assertEquals("bar", $all["foo"]);
        
        CustomFields::set("foo", "other_value", $id);
        $this->assertEquals("other_value", CustomFields::get("foo", $id));
        
        CustomFields::set("foo", null, $id);
        $this->assertNull(CustomFields::get("foo", $id));
        
        
        CustomFields::set("hello", null, $id);
        $this->assertNull(CustomFields::get("hello", $id));
        
        $all = CustomFields::getAll($id);
        $this->assertGreaterThanOrEqual(0, count($all));
    }
}