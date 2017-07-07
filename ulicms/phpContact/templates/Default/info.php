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
 * Kein direkter Zugang
 */
  defined( '_VALID_B4YF' ) or die( 'Restricted access' );



/**
 * Informationen
 */
  $phpContact_template_name = "Default"; // Name des Templates (muss genau so heißen wie der Ordnername!)
  $phpContact_template_version = "4.5"; // Versionsnummer dieses Templates
  $phpContact_template_work = "v1.3.0"; // Funktionalität getestet mit diesen phpContact Versionen (mehrere mit Strichpunkt trennen)
  $phpContact_template_datum = "22.05.2013"; // Datum
  $phpContact_template_type = "Kontaktformular"; // Type
  $phpContact_template_package = "lite"; // "full" nur für die Vollversion geeignet; "lite" für alle Versionen
  $phpContact_template_language = "deutsch, english"; // wählbare Sprachen
  $phpContact_template_charset = "UTF-8, iso-8859-1"; // wählbare Zeichencodierungen
  $phpContact_template_autor = "Günther Hörandl"; // Name des Autors (optional)
  $phpContact_template_web = "www.gh-webdesign.at"; // Web-Adresse des Autors (optional)



/**
 * Changelog
 * ---------
 *
 * v4.5 [22.05.2013]
 *   - TNG Framework Stable 1.0
 *   - Sprachdateien als .ini Dateien
 *   - keine portugisische Sprache mehr
 *   - Bugfix: Logo-URL bei der Integration in Joomla
 *   - erweiterte Parameter (Pflichtfeld - optionales Eingabefeld)
 *   - Parametertyp "color", als Colorpicker für alle Farbeinstellungen
 *
 * v4.1 [22.10.2011]
 *   - portugisische Sprachdatei hinzugefügt
 *   - Texte für die Konfiguration im Admin in die Sprachdatei gepackt
 *   - CSS (layout) etwas geändert (verbreitert)
 *
 * v4.0 [10.05.2011]
 *   - komplette Überarbeitung
 *   - verbesserte Parameterfunktionen
 *   - englische Sprachdatei
 *   - komprimierte CSS (PHP Datei)
 *
 * v3.0 [09.02.2011] :: war nur als BETA verfügbar
 *   - administrative Parameter
 *
 * v2.1 [28.05.2010]
 *   - JavaScript Fehlermeldung beim IE beseitigt
 *   - ALLE Texte in die Sprachdateien ausgelagert (somit auch komplette Zeichencodierung)
 *   - verschiedene Sprachdateien wählbar
 *   - Anzeige der Reloadsprerre verschwindet automatisch nach Ablauf der Zeit
 *   - Verbesserung des XHTML-Codes (Überschriftenstruktur)
 *   - Stylesheets überarbeitet und verbessert
 *   - optimiert und getestet IE6, IE7, IE8, Firefox
 *     (der IE5.5 kann leider keine trasparente Grafik darstellen)
 *
 * v2.0 [31.01.2010]
 *   notwendige Änderungen wegen der Template-Administration
 *
 * v1.1 [31.10.2009]
 *   Bugfix: Include - IP-Sperre Downcount
 *
 * v1.0
 *   first Release
 */
 
?>