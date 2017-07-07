<?php
/**
 * @version   v1.0 - 06.04.2013
 * @package   TNG Framework
 * @author    Günther Hörandl <info@phpcontact.net>
 * @copyright Copyright (c) 2009 - 2013 by Günther Hörandl
 * @license   http://www.phpcontact.net/license.html, see LICENSE.txt
 * @link      http://www.phpcontact.net/
 */

/**
 * diese Datei greift direkt in das Template ein und ändert folgende 'Placeholder':
 * - {css} : entweder auf "style" oder "mobile", je nachdem welche CSS geladen werden soll
 * - {mobileButton-display} : entweder leer oder "hidden" um es als CSS Eingenschft zu verwenden
 * - {desktopButton-display} : entweder leer oder "hidden" um es als CSS Eigenschaft zu verwenden
 * - {mobile} : entweder "1" wenn mobil, "0" wenn desktop oder auf einem mobilen Gerät auf "desktop version" umgeschaltet ist
 * - %%GET-MOBILE-VERSION%% : wird mit Konstante "_B4YF_BUTTON_MOBILE" (für den Buttontext "umschalten auf mobile Version") ersetzt
 * - %%GET-DESKTOP-VERSION%% : wird mit Konstante "_B4YF_BUTTON_DESKTOP" (für den Buttontext "umschalten auf mobile Version") ersetzt 
 */



// Kein direkter Zugang
  defined( '_VALID_B4YF' ) or die ( 'Restricted access' );


/**
 * Weichen
 */
  if ( (defined('_B4YF_MOBILECHECK')) || ($b4yf_test_mobi == "1") ) { // Buttons zeigen
      
    if ($b4yf_post_mobi=="0") {
      $b4yf_css = "style";
      $b4yf_wildcard = str_replace("{mobile}", "0", $b4yf_wildcard);
      if ($phpContact_mobile_switch == "no") { // Switch-Buttons in der "settings.php" Datei deaktiviert?
        $b4yf_wildcard = str_replace("{mobileButton-display}", "hidden", $b4yf_wildcard);
      } else {
        $b4yf_wildcard = str_replace("{mobileButton-display}", "", $b4yf_wildcard);
      }
      $b4yf_wildcard = str_replace("{desktopButton-display}", "hidden", $b4yf_wildcard);
    } else {
      $b4yf_css = "mobile";
      $b4yf_wildcard = str_replace("{mobile}", "1", $b4yf_wildcard);
      $b4yf_wildcard = str_replace("{mobileButton-display}", "hidden", $b4yf_wildcard);
      if ($phpContact_mobile_switch == "no") { // Switch-Buttons in der "settings.php" Datei deaktiviert?
        $b4yf_wildcard = str_replace("{desktopButton-display}", "hidden", $b4yf_wildcard);
      } else {
        $b4yf_wildcard = str_replace("{desktopButton-display}", "", $b4yf_wildcard);
      }
    }
  } else { // keine Buttons
    $b4yf_wildcard = str_replace("{mobile}", "0", $b4yf_wildcard);
    $b4yf_wildcard = str_replace("{mobileButton-display}", "hidden", $b4yf_wildcard);
    $b4yf_wildcard = str_replace("{desktopButton-display}", "hidden", $b4yf_wildcard);
  }
  
  $b4yf_va_css = $b4yf_css; // notwenig wegen dem Joomla! Plugin
  
  // Replaces
  $b4yf_wildcard = str_replace("{css}", $b4yf_css, $b4yf_wildcard);
  $b4yf_wildcard = str_replace("%%GET-MOBILE-VERSION%%", _B4YF_BUTTON_MOBILE, $b4yf_wildcard);
  $b4yf_wildcard = str_replace("%%GET-DESKTOP-VERSION%%", _B4YF_BUTTON_DESKTOP, $b4yf_wildcard);

?>