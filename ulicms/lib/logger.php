<?php
define ( "LOG_TIME_FORMAT", "H:i:s" );
function log_db_query($query) {
	Logger::logDbQuery ( $query );
}
