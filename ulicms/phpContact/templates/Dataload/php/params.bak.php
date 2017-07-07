<?php
/**
 * @version   v2.5 - 26.05.2013
 * @package   phpContact 1.3.0 - Template: Dataload
 * @author    Günther Hörandl <info@phpcontact.net>
 * @copyright Copyright (c) 2009 - 2013 by Günther Hörandl
 * @license   http://www.phpcontact.net/license.html, see LICENSE.txt
 * @link      http://www.phpcontact.net/
 */

/**
 * Default Einstellungen der Parameter
 *
 * Struktur:
 * TYPE | NAME | WERT | aktiver Wert, oder mögliche Werte (mit Beistrich trennen) | LABEL (für Adminbereich) | INFO (für Adminbereich) 
 */

/*
$b4yf_param[1]='seperator|1||%%SEP01_INFO%%|%%SEP01_HEAD%%|%%SEP01_INFO%%';
$b4yf_param[2]='feldactivate|feld:1|active|active|%%FIRMA%%|';
*/

$b4yf_param[1]='seperator|1||%%SEP01_INFO%%|%%SEP01_HEAD%%|%%SEP01_INFO%%';

$b4yf_param[2]='text|var:phpContact_width||600px|%%VAR01_HEAD%%|%%VAR01_INFO%%';
$b4yf_param[3]='select|var:phpContact_align|left,center,right|left|%%VAR02_HEAD%%|%%VAR02_INFO%%';
$b4yf_param[4]='select|var:phpContact_language|deutsch,english,portugues|deutsch|%%VAR03_HEAD%%|%%VAR03_INFO%%';
$b4yf_param[5]='select|var:phpContact_charset|utf_8,iso_8859_1|iso_8859_1|%%VAR04_HEAD%%|%%VAR04_INFO%%';

$b4yf_param[6]='seperator|2||%%SEP02_INFO%%|%%SEP02_HEAD%%|%%SEP02_INFO%%';
$b4yf_param[7]='color|var:phpContact_page_backgroundcolor||#FFFFFF|%%VAR05_HEAD%%|%%VAR05_INFO%%';
$b4yf_param[8]='color|var:phpContact_block_backgroundcolor||#F3F3F3|%%VAR06_HEAD%%|%%VAR06_INFO%%';
$b4yf_param[9]='color|var:phpContact_block_bordercolor||#BFC0FF|%%VAR07_HEAD%%|%%VAR07_INFO%%';

$b4yf_param[10]='seperator|3||%%SEP03_INFO%%|%%SEP03_HEAD%%|%%SEP03_INFO%%';
$b4yf_param[11]='textarea|wildcard:HEADER||%%{WILDCARD01_VALUE}%%|%%WILDCARD01_HEAD%%|%%WILDCARD01_INFO%%';
$b4yf_param[12]='textarea|wildcard:FOOTER||%%{WILDCARD02_VALUE}%%|%%WILDCARD02_HEAD%%|%%WILDCARD02_INFO%%';

$b4yf_param[13]='seperator|4||%%SEP04_INFO%%|%%SEP04_HEAD%%|%%SEP04_INFO%%';
$b4yf_param[14]='feldactivate|feld:1|active|active|%%NAME%%|';
$b4yf_param[15]='feldactivate|feld:2|active|active|%%EMAIL%%|';
$b4yf_param[16]='feldactivate|feld:3|active|active|%%DATEI1%%|';
$b4yf_param[17]='feldactivate|feld:4|active|active|%%DATEI2%%|';
$b4yf_param[18]='feldactivate|feld:5|active|active|%%DATEI3%%|';
$b4yf_param[19]='feldactivate|feld:6|active|active|%%DATEI4%%|';
$b4yf_param[20]='feldactivate|feld:7|active|active|%%DATEI5%%|';
$b4yf_param[21]='feldactivate|feld:8|active||%%DATEI6%%|';
$b4yf_param[22]='feldactivate|feld:9|active||%%DATEI7%%|';
$b4yf_param[23]='feldactivate|feld:10|active||%%DATEI8%%|';
$b4yf_param[24]='feldactivate|feld:11|active||%%DATEI9%%|';
$b4yf_param[25]='feldactivate|feld:12|active||%%DATEI10%%|';
$b4yf_param[26]='feldactivate|feld:13;feld:14|active|active|%%CAPTCHA%%|';

$b4yf_param[27]='seperator|5||%%SEP05_INFO%%|%%SEP05_HEAD%%|%%SEP05_INFO%%';
$b4yf_param[28]='text|var:phpContact_anhang1_allowed||zip,pdf,jpg,png,gif,mp3,txt,doc,xls|%%VAR08_HEAD%%|%%VAR08_INFO%%';
$b4yf_param[29]='text|var:phpContact_anhang1_size||3145728|%%VAR09_HEAD%%|%%VAR09_INFO%%';
$b4yf_param[30]='checkbox|var:phpContact_anhang1_pflicht|ja|ja|%%VAR10_HEAD%%|%%VAR10_INFO%%';
$b4yf_param[31]='text|var:phpContact_anhang2_allowed||zip,pdf,jpg,png,gif,mp3,txt,doc,xls|%%VAR11_HEAD%%|%%VAR11_INFO%%';
$b4yf_param[32]='text|var:phpContact_anhang2_size||1048576|%%VAR12_HEAD%%|%%VAR12_INFO%%';

?>