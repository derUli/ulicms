<?php
class DBMigrator {
	private $component = null;
	private $folder = null;
	public function __construct($component, $folder) {
		$this->component = $component;
		$this->folder = $folder;
	}
	public function run() {
		if (isNullOrEmpty ( $this->component )) {
			throw new Exception ( "component is null or empty" );
		}
		if (isNullOrEmpty ( $this->folder )) {
			throw new Exception ( "folder is null or empty" );
		}
		if (! is_dir ( $this->folder )) {
			throw new Exception ( "folder not found " . $this->folder );
		}
		$files = scandir ( $this->folder );
		natcasesort ( $files );
		foreach ( $files as $file ) {
			if (endsWith ( $file, ".sql" )) {
				$sql = "SELECT id from {prefix}dbtrack where component = ? and name = ?";
				$args = array (
						$this->component,
						$file 
				);
				$result = Database::pQuery ( $sql, $args, true );
				if (Database::getNumRows ( $result ) == 0) {
					$path = $this->folder . "/" . $file;
					$sql = file_get_contents ( $path );
					$cfg = new config ();
					$sql = str_ireplace ( "{prefix}", $cfg->db_prefix, $sql );
					Database::query ( $sql, true );
					$sql = "INSERT INTO {prefix}dbtrack (component, name) values (?,?)";
					Database::pQuery ( $sql, $args, true );
				}
			}
		}
	}
}