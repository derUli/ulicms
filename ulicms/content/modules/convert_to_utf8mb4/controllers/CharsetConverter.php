<?php
class CharsetConverter extends Controller {
	public function convertTOUTF8MB4($table) {
		return Database::pQuery ( "ALTER TABLE $table CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" );
	}
}