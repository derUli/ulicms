<?php
class ContactBook extends Controller {
	public function getSettingsLinkText() {
		return get_translation ( "manage_contacts" );
	}
	public function getSettingsHeadline() {
		return get_translation ( "contact_book" );
	}
	public function settings() {
		return "Not implemented yet";
	}
	public function render() {
		return "Not implemented yet";
	}
}