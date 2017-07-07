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
 * diese Datei wird in die Template-Engine für die E-Mail Erstellung geladen (am Ende nach allen anderen Prozeduren)
 * Verwendung findet hier die Variabele $b4yf_wildcard
 * 
 * Beispiel:
 *   wird im E-Mail Template das Wildcard §§IP-ADRESSE§§ gesetzt, dann kann mit folgendem Code dieser durch die IP Adresse ersetzt werden:
 *   $b4yf_wildcard = str_replace("§§IP§§", $_SERVER['SERVER_ADDR'], $b4yf_wildcard);
 *
 * ACHTUNG:
 *   KEINE Wildcards mit ##wildcard## und %%wildcard%% verwenden!!!
 *   das Wildcard: {wildcard} funktioniert.
 *   folgendes Wildcard wäre besser: §§wildcard§§
 */



/**
 * Kein direkter Zugang
 */
  defined( '_VALID_B4YF' ) or die( 'Restricted access' );



/**
 * IP Adresse übermitteln
 */
  // $b4yf_wildcard = str_replace("§§IP§§", $_SERVER['SERVER_ADDR'], $b4yf_wildcard); // IP-Adresse übermitteln
  

/**
 * nicht gewählte Select Übergabe löschen
 */
  $b4yf_wildcard = str_replace(_B4YF_LAND_OPTION_ERROR, "", $b4yf_wildcard); // nicht gewählter Ort

?>