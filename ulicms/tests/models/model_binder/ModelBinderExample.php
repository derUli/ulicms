<?php

class ModelBinderExample extends Model {

    protected $field1;
    protected $field2;
    protected $my_field;
    protected $not_mapped;
    protected $notMappedTwo;

    public function setField1($value) {
        Model::checkValueType($value, "int");
        $this->field1 = $value;
    }

    public function setField2($value) {
        Model::checkValueType($value, "string");
        $this->field2 = $value;
    }

    public function setMyField($value) {
        Model::checkValueType($value, "numeric");
        $this->field3 = $value;
    }

    public function getField1() {
        return $this->field1;
    }

    public function getField2() {
        return $this->field2;
    }

    public function getMyField() {
        return $this->my_field;
    }

    public function getNotMapped() {
        return $this->not_mapped;
    }

    private $sampleValuesOK = array(
        "field1" => 123,
        "field2" => "This is string"
    );
    private $invalidValues1 = array(
        "my_field" => "This should be a number"
    );
    private $invalidValues2 = array(
        "not_mapped" => "Not mapped"
    );

    public function fillVars($data = null) {
        $this->bindValues($this->sampleValuesOK);
    }

    public function fillInvalid1() {
        $this->bindValues($this->invalidValues1);
    }

    public function fillInvalid2() {
        $this->bindValues($this->invalidValues2);
    }

}
