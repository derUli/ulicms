<?php

class CustomFieldsTest extends \PHPUnit\Framework\TestCase {
    protected function tearDown(): void {
        $id = $this->getFirstPage()->id;
        $type = $this->getFirstPage()->type;
        Database::pQuery('delete from {prefix}custom_fields where name in (?, ?) and content_id = ?', [
            "{$type}_foo",
            "{$type}_hello",
            (int)$id
        ], true);

        \App\Storages\Vars::delete('id');

        $_GET = [];
        $_REQUEST = [];
        $_POST = [];
    }

    public function testSetAndGetField() {
        $id = $this->getFirstPage()->id;
        $type = $this->getFirstPage()->type;

        CustomFields::set('foo', 'bar', $id, true);
        $this->assertEquals('bar', CustomFields::get('foo', $id));

        CustomFields::set('hello', 'world', $id, true);
        $this->assertEquals('world', CustomFields::get('hello', $id));

        $all = CustomFields::getAll($id);
        $this->assertGreaterThanOrEqual(2, count($all));

        $this->assertEquals('world', $all['hello']);
        $this->assertEquals('bar', $all['foo']);

        CustomFields::set('foo', 'other_value', $id, true);
        $this->assertEquals('other_value', CustomFields::get('foo', $id));

        CustomFields::set('foo', null, $id, true);
        $this->assertNull(CustomFields::get('foo', $id));

        CustomFields::set('hello', null, $id, true);
        $this->assertNull(CustomFields::get('hello', $id));

        $all = CustomFields::getAll($id);
        $this->assertGreaterThanOrEqual(0, count($all));
    }

    public function testSetAndGetBooleanToFalse() {
        $id = $this->getFirstPage()->id;

        $uniq = uniqid();

        CustomFields::set($uniq, false, $id, true);

        $this->assertEquals('0', CustomFields::get($uniq, $id));

        CustomFields::set($uniq, null);
    }

    public function testSetAndGetBooleanToTrue() {
        $id = $this->getFirstPage()->id;

        $uniq = uniqid();

        CustomFields::set($uniq, true, $id, true);

        $this->assertEquals('1', CustomFields::get($uniq, $id));

        CustomFields::set($uniq, null);
    }

    public function testSetAndGetFieldArray() {
        $id = $this->getFirstPage()->id;

        $value = ['foo', 'bar'];
        CustomFields::set('foo', $value, $id, true);

        $this->assertEquals($value, CustomFields::get('foo', $id));
    }

    public function testSetAndGetFieldArrayWithoutId() {
        $page = $this->getFirstPage();
        set_requested_pagename($page->slug, $page->language);

        CustomFields::set('foo', 'bar', null, false);
        $this->assertEquals('bar', CustomFields::get('foo', null, false));
    }

    public function testSetAndGetAllBooleanToTrueWithoutId() {
        $page = $this->getFirstPage();
        set_requested_pagename($page->slug, $page->language);

        CustomFields::set('foo', 'bar');

        $all = CustomFields::getAll();
        $this->assertEquals('bar', $all['foo']);
    }

    private function getFirstPage() {
        $pages = ContentFactory::getAll();
        return $pages[0];
    }
}
