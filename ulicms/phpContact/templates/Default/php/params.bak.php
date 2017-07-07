<?php
/**
 * @version   v4.5 - 22.05.2013
 * @package   phpContact 1.3.0 - Template: Default
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


$b4yf_param[1]='seperator|1||%%SEP01_INFO%%|%%SEP01_HEAD%%|%%SEP01_INFO%%';

$b4yf_param[2]='activate|feld:1|active|active|%%FIRMA%%|%%ACTIVATE%%'; // Firma
$b4yf_param[3]='required|feld:1|yes||%%FIRMA%%|%%REQURIED%%';

$b4yf_param[4]='activate|feld:2|active|active|%%VORNAME%%|%%ACTIVATE%%'; // Vorname
$b4yf_param[5]='required|feld:2|yes|yes|%%VORNAME%%|%%REQURIED%%';

$b4yf_param[6]='activate|feld:3|active|active|%%FAMILIENNAME%%|%%ACTIVATE%%'; // Familienname
$b4yf_param[7]='required|feld:3|yes|yes|%%FAMILIENNAME%%|%%REQURIED%%';

$b4yf_param[8]='activate|feld:4;feld:5|active|active|%%STRASSE_NR%%|%%ACTIVATE%%'; // Straße / Nr.
$b4yf_param[9]='required|feld:4;feld:5|yes|yes|%%STRASSE_NR%%|%%REQURIED%%';

$b4yf_param[10]='activate|feld:6;feld:7|active|active|%%PLZ_ORT%%|%%ACTIVATE%%'; // PLZ / Ort
$b4yf_param[11]='required|feld:6;feld:7|yes|yes|%%PLZ_ORT%%|%%REQURIED%%';

$b4yf_param[12]='activate|feld:8|active|active|%%LAND%%|%%ACTIVATE%%'; // Land
$b4yf_param[13]='required|feld:8|yes|yes|%%LAND%%|%%REQURIED%%';

$b4yf_param[14]='activate|feld:9|active|active|%%TEL%%|%%ACTIVATE%%'; // Tel
$b4yf_param[15]='required|feld:9|yes||%%TEL%%|%%REQURIED%%';

$b4yf_param[16]='activate|feld:10|active|active|%%EMAIL%%|%%ACTIVATE%%'; // E-Mail
$b4yf_param[17]='required|feld:10|yes|yes|%%EMAIL%%|%%REQURIED%%';

$b4yf_param[18]='activate|feld:11|active|active|%%BETREFF%%|%%ACTIVATE%%'; // Betreff
$b4yf_param[19]='required|feld:11|yes|yes|%%BETREFF%%|%%REQURIED%%';

$b4yf_param[20]='activate|feld:12|active|active|%%NACHRICHT%%|%%ACTIVATE%%'; // Nachricht
$b4yf_param[21]='required|feld:12|yes|yes|%%NACHRICHT%%|%%REQURIED%%';

$b4yf_param[22]='feldactivate|feld:13;feld:14|active|active|%%CAPTCHA%%|%%ACTIVATE%%'; // Captcha


$b4yf_param[23]='seperator|2||%%SEP02_INFO%%|%%SEP02_HEAD%%|%%SEP02_INFO%%';
$b4yf_param[24]='color|var:phpContact_color||#f90|%%VAR01_HEAD%%|%%VAR01_INFO%%<ul><li>#FF9900</li><li>#666</li><li>rgb(255,128,0)</li><li>blue</li></ul>';
$b4yf_param[25]='text|var:phpContact_width||400px|%%VAR02_HEAD%%|%%VAR02_INFO%%';
$b4yf_param[26]='select|var:phpContact_align|left,center,right|left|%%VAR03_HEAD%%|%%VAR03_INFO%%';
$b4yf_param[27]='select|var:phpContact_language|deutsch,english|deutsch|%%VAR04_HEAD%%|%%VAR04_INFO%%';
$b4yf_param[28]='select|var:phpContact_charset|utf-8,iso-8859-1|utf-8|%%VAR05_HEAD%%|%%VAR05_INFO%%';
$b4yf_param[29]='select|var:phpContact_logo|ja,nein|ja|%%VAR06_HEAD%%|%%VAR06_INFO%%';

$b4yf_param[30]='seperator|3||%%SEP03_INFO%%|%%SEP03_HEAD%%|%%SEP03_INFO%%';
$b4yf_param[31]='textarea|wildcard:HEADER||%%{WILDCARD01_VALUE}%%|%%WILDCARD01_HEAD%%|%%WILDCARD01_INFO%%';
$b4yf_param[32]='textarea|wildcard:FOOTER||%%{WILDCARD02_VALUE}%%|%%WILDCARD02_HEAD%%|%%WILDCARD02_INFO%%';

?>