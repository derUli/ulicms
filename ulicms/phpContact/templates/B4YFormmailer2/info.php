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
 * Kein direkter Zugang
 */
  defined( '_VALID_B4YF' ) or die( 'Restricted access' );



/**
 * Informationen
 */
  $phpContact_template_name = "B4YFormmailer2"; // Name des Templates (muss genau so heißen wie der Ordnername!)
  $phpContact_template_version = "2.5"; // Versionsnummer dieses Templates
  $phpContact_template_work = "v1.3.0"; // Funktionalität getestet mit diesen phpContact Versionen (mehrere mit Strichpunkt trennen)
  $phpContact_template_datum = "24.05.2013"; // Versionsnummer dieses Templates
  $phpContact_template_type = "Multifunktionsformular"; // Art des Formulars
  $phpContact_template_package = "lite"; // "full" nur für die Vollversion geeignet; "lite" für alle Versionen
  $phpContact_template_language = "deutsch, english, portugues"; // verwendete Sprachen
  $phpContact_template_charset = "utf-8, iso-8859-1"; // verwendete Zeichencodierungen
  $phpContact_template_autor = "Günther Hörandl"; // Name des Autors (optional)
  $phpContact_template_web = "www.gh-webdesign.at"; // Web-Adresse des Autors (optional)



/**
 * Changelog
 * ---------
 *
 * v2.3
 *   - Parametertyp "color", als Colorpicker für alle Farbeinstellungen
 *
 * v2.2
 *   - Parameter Sprachdateien ausgelagert
 *   - nur noch eine deutsche Sprachdatei (formal)
 *   - 2 neue Sprachdateien "english" und "portugues"
 *
 * v2.1 (in phpContact v1.2.0 includiert)
 *   - Parameter Felder Aktivierung (nicht deaktivieren)
 *   - CSS (PHP Datei) komprimiert
 *   - 2 Sprachig (deutsch formal und deutsch informal) per Parameter wählbar
 *   - Sprachdateien NUR noch UTF-8, aber per Parameter auch als iso-8859-1 Ausgabe
 *
 * v2.0 (in phpContact v1.1.0 includiert)
 *   Paramtersteuerung
 *   - alle Farben
 *   - Rahmenstil (durchgezogen, gepunktet, strichliert oder kein Rahmen)
 *   - Breite des Formulars (in Pixel oder in Prozent)
 *   - Ausrichtung (links, zentriert oder rechts)
 *   - Anhang: maximale Größe und nicht erlaubte Dateitypen
 *   - Sprache (Deutsch formal iso-8859-1, Deutsch formal utf-8, Deutsch informal iso-8859-1 oder Deutsch informal utf-8)
 *   - Einleitungstext und Footetext im Admin
 *   - alle Eingabefelder können deaktiviert werden
 *   - alle Bereiche können unsichtbar geschalten werden
 *   3 zusätzliche (optionale) Eingabefelder (Firma, Name und Homepageadresse)
 *   Inaktiver "Senden" Button bei der Reloadsperre mit automatischer Aktivierung
 *   Warnmeldung wenn das Anhang-Feld geleert wurde
 *   Infomeldung wenn das Captcha-Feld geleert wurde
 *
 * v1.0 (in phpContact v1.0.0 includiert)
 *   first Release
 */
 
?>