#!/usr/bin/env php
<?php
// wrapper for the tools in the "shell" subfolder
if (! (php_sapi_name () === 'cli' or defined ( 'STDIN' ))) {
	header ( "HTTP/1.0 403 Forbidden" );
	echo "This is a command line only script.";
	exit ();
}

$rootDir = dirname ( __FILE__ );

// first element of $argv is always the name of this script
// we don't need it, so remove it
array_shift ( $argv );

// remove the command (e.g. "sinfo") from the string and put it into a variable
$command = array_shift ( $argv );

// add quotes to the arguments
// to prevent issues with spaces
foreach ( $argv as $key => $value ) {
	$argv [$key] = '"' . $value . '"';
}

// join all arguments to a string;
$fullArgv = implode ( " ", $argv );

// path to the given script file
$script = $rootDir . "/shell/" . basename ( $command ) . ".php";

// if there is a script for this command execute it and passthru it's output to the command line
if (is_file ( $script )) {
	// if we are are not on windows and the executable bit for the script file is not set
	// we stop here with an error.
	if (! defined ( 'PHP_WINDOWS_VERSION_MAJOR' ) and ! is_executable ( $script )) {
		echo "Error: " . basename ( $script ) . " is not executable.\n\n";
		echo "Please run this command to make the file executable:\n";
		echo "chmod +x \"{$script}\"\n";
		exit ();
	}
	// execute the script with the given arguments and print its outputs to the command line
	passthru ( "\"" . $script . "\" $fullArgv" );
	exit ();
}

// Print usage help
echo "Usage:\n./" . basename ( __FILE__ ) . " [command] [arguments]\n\n";

echo "Available commands:\n\n";

$scripts = glob ( $rootDir . "/shell/*.php" );

foreach ( $scripts as $script ) {
	$cmd = trim ( str_replace ( ".php", "", basename ( $script ) ) );
	echo "* {$cmd}\n";
}

// Show some example usage commands
echo "\n";
echo "Examples:\n";
echo "./" . basename ( __FILE__ ) . " sinfo themes\n";
echo "./" . basename ( __FILE__ ) . " settings_get email\n";
echo "./" . basename ( __FILE__ ) . ' settings_set foo "My Foobar"' . "\n";

exit ();
