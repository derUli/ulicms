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
 * templatespezifische Einstellungen, die nicht im Administratorbereich geändert werden können
 * funktionieren erst seit Version 1.3.0
 */



/**
 * Kein direkter Zugang
 */
  defined( '_VALID_B4YF' ) or die( 'Restricted access' );



/**
 * Label der Pflichtfelder
 * ohne Angaben werden folgende Defaultwerte verwendet:
 * - TNG:
 *        $b4yf_required_feld_before = '<span class="b4yf_required">';
 *        $b4yf_required_feld_after = '</span>';
 * - kein Framework:
 *        $b4yf_required_feld_before = '';
 *        $b4yf_required_feld_after = '';
 */
  $b4yf_required_feld_before = '<strong>'; // wird dem Label vorangestellt
  $b4yf_required_feld_after = '</strong>'; // wird dem Label hinterhergestellt



/**
 * Target der Copyrightlinks
 * ohne Angabe wird der Link im gleichen Fenster geöffnet
 */
  $b4yf_copyright_target = "_blank";



/** 
 * Captcha
 * Höhe und Breite wird automatisch an der Größe der Hintergrundgrafik ermittelt
 * ohne Angaben werden folgende Standardwerte verwendet
 * - $b4yf_color_default = "999999";
 * - $b4yf_background_default = $b4yf_path_root.'captcha/bg.png';
 * - $b4yf_font_default = $b4yf_path_root.'captcha/font.ttf';
 * - $b4yf_stellen_default = 5;
 */
  // $b4yf_captcha_color = "888888"; // Farbe der Zeichen in 6 stelligem HEX ohne #
  // if (isset($b4yf_path_root)) $b4yf_captcha_background = $b4yf_path_root.'templates/Amela/captcha/background.png'; // Hintergrundgrafik
  // if (isset($b4yf_path_root)) $b4yf_captcha_font = $b4yf_path_root.'templates/Amela/captcha/font.ttf'; // Schriftart
  // $b4yf_captcha_stellen = 5; // Anzahl der Zeichen (Stellen der Zahlen)



/**
 * Joomla Message
 * Wenn phpContact im CMS Joomla verwendet wird, kann die Joomla-interne Messageausgabe angezapft werden
 * yes = Joomla Message verwenden und phpContact Message ausschalten (außer der Reloadsperre)
 * no = keine Joomla Message, alle Messages über phpContact
 */
  // wird nicht verwendet !!!



/**
 * Mobiles Theme verwenden (wenn vorhanden)
 * yes = bei Erkennung eines mobilen Endgerätes auf das mobile Theme umschalten
 * no = auch bei moblilen Endgeräten das Desktop-Theme anzeigen
 */
  // wird nicht verwendet !!!



/**
 * Mobile Buttons anzeigen
 * yes = beim Mobilen Themes die Buttons zum Umschalten auf das Desktop bzw. mobile Theme anzeigen.
 * no = keine Buttons anzeigen
 */
  // wird nicht verwendet !!!



/**
 * Wenn die HTML5 Einabefeldertypen (number, tel, email und url) nicht verwendet werden sollen, dann kann das mit dieser Variabele verhindert werden.
 * yes = HTML5 Eingabefelder verwenden
 * no = HTML5 Eingabefelder NICHT verwenden und stattdessen den "input" Type verwenden
 */
  // wird nicht verwendet !!!

?>