<?php
class BackendHelper {
	public static function formatDatasetCount($count) {
		if ($count == 1) {
			translate ( "ONE_DATASET_FOUND" );
		} else {
			translate ( "X_DATASETS_FOUND", array (
					"%x" => $count 
			) );
		}
	}
}