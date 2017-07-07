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
 * Kein direkter Zugang
 */
  defined( '_VALID_B4YF' ) or die( 'Restricted access' );



/**
 * Informationen
 */
  $phpContact_template_name = "Dataload"; // Name des Templates (muss genau so heißen wie der Ordnername!)
  $phpContact_template_version = "2.5"; // Versionsnummer dieses Templates
  $phpContact_template_work = "v1.3.0"; // Funktionalität getestet mit diesen phpContact Versionen (mehrere mit Strichpunkt trennen)
  $phpContact_template_datum = "26.05.2013"; // Erstellungsdatum dieses Templates
  $phpContact_template_type = "Upload-Formular"; // Versionsnummer dieses Templates
  $phpContact_template_package = "lite"; // "full" nur für die Vollversion geeignet; "lite" für alle Versionen
  $phpContact_template_language = "deutsch, english, portugues"; // verwendete Sprache
  $phpContact_template_charset = "UTF-8, iso-8859-1"; // verwendete Zeichencodierung
  $phpContact_template_autor = "Günther Hörandl"; // Name des Autors (optional)
  $phpContact_template_web = "www.gh-webdesign.at"; // Web-Adresse des Autors (optional)



/**
 * Changelog
 * ---------
 *
 * v2.5 [26.05.2013]
 *   - TNG Framework Stable 1.0
 *   - Sprachdateien als .ini Dateien
 *   - Parametertyp "color", als Colorpicker für alle Farbeinstellungen
 *
 * v2.2 [03.12.2011]
 *   - Sprachdateien für die Parameterkonfiguration im Adminbereich
 *   - Sprachdateien "english" und "portugues" hinzugefügt
 *
 * v2.1 [13.05.2011]
 *   - komplette Überarbeitung
 *   - verbesserte Parameterfunktionen
 *   - Zeichencodierung iso-8859-1 möglich
 *   - komprimierte CSS (PHP Datei)
 *
 * v2.0 [16.04.2011]
 *   - Parametersteuerung eingebaut
 *     - Breite, Ausrichtung, Sprache
 *     - Einleitungstext und Infotext
 *     - Farbe (Bereichstitel)
 *     - Eingabefelder deaktivieren/aktivieren
 *     - separate Konfiguration der erlaubten Dateien
 *     - separate Konfiguration der erlaubten Dateigröße
 *   - Dateiuploadfelder auf 10 Dateien erhöht
 *
 * v1.1 [28.05.2010]
 *   - JavaScript Fehlermeldung beim IE beseitigt
 *   - ALLE Texte in die Sprachdateien ausgelagert (somit auch komplette Zeichencodierung)
 *   - Anzeige der Reloadsprerre verschwindet automatisch nach Ablauf der Zeit
 *   - Stylesheets überarbeitet und verbessert
 *   - optimiert und getestet IE6, IE7, IE8, Firefox
 *     (der IE5.5 kann leider keine trasparente Grafik darstellen)
 *
 * v1.0
 *   first Release
 */

?>