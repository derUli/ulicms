<?php
abstract class Content {
	abstract protected function loadByID($id);
	abstract protected function loadByNameAndLanguage($name, $language = null);
	abstract protected function update();
	abstract protected function create();
	abstract protected function save();
}