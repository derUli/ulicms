<?php
class ContentType {
	public $show = array ();
	public $hide = array ();
	public function toJSON() {
		return json_encode ( array (
				"show" => $this->show,
				"hide" => $this->hide 
		) );
	}
}