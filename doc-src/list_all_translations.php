<?php
require_once 'init.php';
@session_start();

$languages = getAllLanguages ();

if (! empty ( $_GET ["language"] ) and in_array ( $_GET ["language"], $languages )) {
	$_SESSION ["language"] = db_escape ( $_GET ["language"] );
}

if (! isset ( $_SESSION ["language"] )) {
	$_SESSION ["language"] = Settings::get ( "default_language" );
}

setLocaleByLanguage ();

if (in_array ( $_SESSION ["language"], $languages )) {
	require getLanguageFilePath ( $_SESSION ["language"] );
	Translation::loadAllModuleLanguageFiles ( $_SESSION ["language"] );
	Translation::includeCustomLangFile ( $_SESSION ["language"] );
	do_event ( "custom_lang_" . $_SESSION ["language"] );
}

$cons = get_defined_constants();
$translations = [];
foreach($cons as $key=>$value){
	if(startsWith($key, "TRANSLATION_")){
		$translations[$key] = $value;
	}
}