<?php
class ContentType {
	public $show = array ();
	public $customFieldTabTitle;
	public $customFields = array();
	public function toJSON() {
		return json_encode ( array (
				"show" => $this->show 
		) );
	}
}