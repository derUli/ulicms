<?php
class ConvertToUTF8MB4 extends Controller {
	private $moduleName = "convert_to_utf8mb4";
	public function getSettingsHeadline() {
		return get_translation ( "convert_to_utf8mb4" );
	}
	public function getSettingsLinkText() {
		return $this->getSettingsHeadline ();
	}
	public function settings() {
		return Template::executeModuleTemplate ( $this->moduleName, "form.php" );
	}
	public function convertTable() {
		$percent = 0;
		if (! isset ( $_SESSION ["convert_tables"] )) {
			$tables = Database::getAllTables ();
			
			$filteredTables = array ();
			for($i = 0; $i < count ( $tables ); $i ++) {
				if (startsWith ( $tables [$i], tbname ( "" ) )) {
					$filteredTables [] = $tables [$i];
				}
			}
			
			$_SESSION ["convert_tables"] = $filteredTables;
			$_SESSION ["current_conversion"] = 0;
			
			$this->showPercent ( $percent, "" );
		} else {
			$index = $_SESSION ["current_conversion"];
			$charsetConverter = ControllerRegistry::get ( "CharsetConverter" );
			$table = $_SESSION ["convert_tables"] [$index];
			$charsetConverter->convertTOUTF8MB4 ( $table );
			$onePercent = 100 / count ( $_SESSION ["convert_tables"] );
			$percent = ($index + 1) * $onePercent; //
			$_SESSION ["current_conversion"] ++;
			$this->showPercent ( $percent, $table );
			if ($index + 1 >= count ( $_SESSION ["convert_tables"] )) {
				unset ( $_SESSION ["convert_tables"] );
				unset ( $_SESSION ["current_conversion"] );
			}
		}
		exit ();
	}
	protected function showPercent($percent, $table) {
		if ($percent >= 100) {
			echo '<!--finish-->';
		}
		echo '<div style="background-color:green;height:50px; width:' . intval ( $percent ) . '%"></div>';
		echo "<div class='info-text-progress'>" . Template::getEscape ( $table ) . "</div>";
	}
}