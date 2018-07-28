#!/usr/bin/env php
<?php
// wrapper for the tools in the "shell" subfolder
if (! (php_sapi_name () === 'cli' or defined ( 'STDIN' ))) {
	header ( "HTTP/1.0 403 Forbidden" );
	echo "This is a command line only script.";
	exit ();
}

$rootDir = dirname ( __FILE__ );

array_shift ( $argv );

$command = array_shift ( $argv );

foreach ( $argv as $key => $value ) {
	$argv [$key] = '"' . $value . '"';
}

$fullArgv = implode ( " ", $argv );

$script = $rootDir . "/shell/" . basename ( $command ) . ".php";
// if there is a script for this command execute it and passthru it's output to the command line
if (is_file ( $script )) {
	if (! defined ( 'PHP_WINDOWS_VERSION_MAJOR' ) and ! is_executable ( $script )) {
		echo "Error: " . basename ( $script ) . " is not executable.\n\n";
		echo "Please run this command to make the file executable:\n";
		echo "chmod +x \"{$script}\"\n";
		exit ();
	}
	passthru ( "php \"" . $script . "\" $fullArgv" );
	exit ();
}

echo "Usage:\n./" . basename ( __FILE__ ) . " [command] [arguments]\n\n";

echo "Available commands:\n\n";

$scripts = glob ( $rootDir . "/shell/*.php" );

foreach ( $scripts as $script ) {
	$cmd = trim ( str_replace ( ".php", "", basename ( $script ) ) );
	echo "* {$cmd}\n";
}

// Show some example usage commands

echo "\nExamples:\n";
echo "./" . basename ( __FILE__ ) . " sinfo themes\n";
echo "./" . basename ( __FILE__ ) . " settings_get email\n";
echo "./" . basename ( __FILE__ ) . ' settings_set foo "My Foobar"' . "\n";

exit ();
