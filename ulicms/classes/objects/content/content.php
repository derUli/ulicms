<?php
abstract class Content {
	abstract protected function loadByID($id);
	abstract protected function loadBySystemnameAndLanguage($name, $language);
	abstract protected function update();
	abstract protected function create();
	abstract protected function save();
}