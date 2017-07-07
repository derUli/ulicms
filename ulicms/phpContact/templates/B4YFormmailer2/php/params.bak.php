<?php
/**
 * @version   v2.5 - 24.05.2013
 * @package   phpContact 1.3.0 - Template: B4YFormmailer2
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

$b4yf_param[2]='activate|feld:1|active|active|%%EMPFAENGER%%|%%ACTIVATE%%'; // Empfänger
$b4yf_param[3]='required|feld:1|yes|yes|%%EMPFAENGER%%|%%REQURIED%%';

$b4yf_param[4]='activate|feld:2|active|active|%%FIRMA%%|%%ACTIVATE%%'; // Firma
$b4yf_param[5]='required|feld:2|yes||%%FIRMA%%|%%REQURIED%%';

$b4yf_param[6]='activate|feld:3|active|active|%%NAME%%|%%ACTIVATE%%'; // Name
$b4yf_param[7]='required|feld:3|yes|yes|%%NAME%%|%%REQURIED%%';

$b4yf_param[8]='activate|feld:4|active|active|%%EMAIL%%|%%ACTIVATE%%'; // E-Mail Adresse
$b4yf_param[9]='required|feld:4|yes|yes|%%EMAIL%%|%%REQURIED%%';

$b4yf_param[10]='activate|feld:5|active|active|%%WEB%%|%%ACTIVATE%%'; // Homepage
$b4yf_param[11]='required|feld:5|yes||%%WEB%%|%%REQURIED%%';

$b4yf_param[12]='activate|feld:6|active|active|%%BETREFF%%|%%ACTIVATE%%'; // Betreff
$b4yf_param[13]='required|feld:6|yes|yes|%%BETREFF%%|%%REQURIED%%';

$b4yf_param[14]='activate|feld:7|active|active|%%NACHRICHT%%|%%ACTIVATE%%'; // Nachricht
$b4yf_param[15]='required|feld:7|yes|yes|%%NACHRICHT%%|%%REQURIED%%';

$b4yf_param[16]='feldactivate|feld:8|active|active|%%ANHANG%%|%%ACTIVATE%%'; // Anhang

$b4yf_param[17]='feldactivate|feld:9;feld:10|active|active|%%TEXT_CAPTCHA%%|%%ACTIVATE%%'; // Captcha

$b4yf_param[18]='feldactivate|feld:11|active|active|%%KOPIE%%|%%ACTIVATE%%'; // Kopie an den Absender

$b4yf_param[19]='feldactivate|feld:13|active|active|%%BUTTON_LOESCHEN_KONFIG%%|%%ACTIVATE%%'; // Button "Löschen"


$b4yf_param[20]='seperator|2||%%SEP02_INFO%%|%%SEP02_HEAD%%|%%SEP02_INFO%%';

$b4yf_param[21]='text|var:phpContact_width||400px|%%VAR01_HEAD%%|%%VAR01_INFO%%';
$b4yf_param[22]='select|var:phpContact_align|left,center,right|left|%%VAR02_HEAD%%|%%VAR02_INFO%%';
$b4yf_param[23]='select|var:phpContact_language|deutsch,english,portugues|deutsch|%%VAR03_HEAD%%|%%VAR03_INFO%%';
$b4yf_param[24]='select|var:phpContact_charset|utf-8,iso-8859-1|utf-8|%%VAR04_HEAD%%|%%VAR04_INFO%%';
$b4yf_param[25]='text|var:phpContact_anhang_disallowed||exe,bat,ini|%%VAR05_HEAD%%|%%VAR05_INFO%%';
$b4yf_param[26]='text|var:phpContact_anhang_size||3145728|%%VAR06_HEAD%%|%%VAR06_INFO%%';

$b4yf_param[27]='seperator|3||%%SEP03_INFO%%|%%SEP03_HEAD%%|%%SEP03_INFO%%';

$b4yf_param[28]='color|var:phpContact_color_h||#EF8700|%%VAR07_HEAD%%|';
$b4yf_param[29]='color|var:phpContact_color_texte||#000|%%VAR08_HEAD%%|';
$b4yf_param[30]='color|var:phpContact_color_bg_page||#FFF|%%VAR09_HEAD%%|';
$b4yf_param[31]='color|var:phpContact_color_bg_form||#F5F5F5|%%VAR10_HEAD%%|';
$b4yf_param[32]='color|var:phpContact_color_bg_fieldset||#F5F5F5|%%VAR11_HEAD%%|';
$b4yf_param[33]='color|var:phpContact_color_bg_captcha||#FFF|%%VAR12_HEAD%%|';
$b4yf_param[34]='color|var:phpContact_color_bg_inputs||#FFF|%%VAR13_HEAD%%|';
$b4yf_param[35]='color|var:phpContact_color_border_form||#AAA|%%VAR14_HEAD%%|';
$b4yf_param[36]='color|var:phpContact_color_border_fieldset||#CCC|%%VAR15_HEAD%%|';
$b4yf_param[37]='color|var:phpContact_color_border_captcha||#AAA|%%VAR16_HEAD%%|';
$b4yf_param[38]='color|var:phpContact_color_border_inputs||#AAA|%%VAR17_HEAD%%|';
$b4yf_param[39]='color|var:phpContact_color_border_inputs_focus||#EF8700|%%VAR18_HEAD%%|';
$b4yf_param[40]='color|var:phpContact_color_captcha||#EF8700|%%VAR19_HEAD%%|%%VAR19_INFO%%';

$b4yf_param[41]='seperator|4||%%SEP04_INFO%%|%%SEP04_HEAD%%|%%SEP04_INFO%%';
$b4yf_param[42]='select|var:phpContact_border_form|solid,dotted,dashed,none|solid|%%VAR20_HEAD%%|';
$b4yf_param[43]='select|var:phpContact_border_fieldset|solid,dotted,dashed,none|dashed|%%VAR21_HEAD%%|';
$b4yf_param[44]='select|var:phpContact_border_captcha|solid,dotted,dashed,none|solid|%%VAR22_HEAD%%|';

$b4yf_param[45]='seperator|5||%%SEP05_INFO%%|%%SEP05_HEAD%%|%%SEP05_INFO%%';
$b4yf_param[46]='textarea|wildcard:HEADER||%%{WILDCARD01_VALUE}%%|%%WILDCARD01_HEAD%%|%%WILDCARD01_INFO%%';
$b4yf_param[47]='textarea|wildcard:FOOTER||%%{WILDCARD02_VALUE}%%|%%WILDCARD02_HEAD%%|%%WILDCARD02_INFO%%';

?>