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


// echo "<h3>mobile/index.php</h3>";
// echo "phpContact_mobile: ".$phpContact_mobile."<br />";
// echo "phpContact_mobile_switch: ".$phpContact_mobile_switch."<br />";


// Kein direkter Zugang
  defined( '_VALID_B4YF' ) or die ( 'Restricted access' );


// Parameter laden
  $b4yf_template_parameter = $b4yf_absolute_path.'includes/paramsget-variables.inc.php';
  if (file_exists($b4yf_template_parameter)) { require($b4yf_template_parameter); }



/**
 * Informationen aus der Template "settings.php" auslesen
 * $b4yf_joomla_message liefert die Information ob die Joomla Message genutzt werden soll (yes) oder die phpContact Message Position (no)
 * wenn keine settings.php vorhanden ist, oder $b4yf_joomla_message nicht definiert ist, dann wir die phpContact Message verwendet 
  */  
  if (file_exists($b4yf_absolute_path.'templates/'.$b4yf_template.'/php/settings.php')) {
    include ($b4yf_absolute_path.'templates/'.$b4yf_template.'/php/settings.php');
  }


/**
 * Defaultwert wenn kein Parameter gesetzt wurde
 */
  if (!isset($phpContact_mobile)) { $phpContact_mobile = "yes"; }


/**
 * benötigte Variablen definieren
 */
  $b4yf_get_mobi = "";
  $b4yf_post_mobi = "";
  $b4yf_css = "style";
  if (!isset($b4yf_mobi)) $b4yf_mobi = "";
  if ( defined('_B4YF_MOBILECHECK')) $b4yf_mobi = true;
  if (!defined('_B4YF_BUTTON_MOBILE')) define('_B4YF_BUTTON_MOBILE','Switch to Mobile');
  if (!defined('_B4YF_BUTTON_DESKTOP')) define('_B4YF_BUTTON_DESKTOP','Switch to Desktop');


/**
 * mobile Browser detection
 * variable "$b4yf_mobi": true wenn mobil; false bei desktop
 * variable "$b4yf_mobiletheme": off wenn mobiles Theme in der "settings.php" Datei deaktiviert
 */
  if ($phpContact_mobile != "no") {
    if (file_exists(dirname(__FILE__).'/detection.php')) { require_once(dirname(__FILE__).'/detection.php'); }
  }



/**
 * GET Parameter Übergabe per Link "http://...?phpContactMobile=1"
 */
  $b4yf_test_mobi = "";
  if ( isset($_GET['phpContactMobile']) && $_GET['phpContactMobile'] == "1" ) { $b4yf_test_mobi = "1"; $b4yf_post_mobi = "1"; }
  if ( isset($_GET['phpContactMobile']) && $_GET['phpContactMobile'] == "0" ) { $b4yf_test_mobi = "0"; $b4yf_post_mobi = "0"; }
  if ( isset($_POST['phpContactTestMobile']) && $_POST['phpContactTestMobile'] == "1" ) { $b4yf_test_mobi = "1"; }
  if ( isset($_POST['phpContactTestMobile']) && $_POST['phpContactTestMobile'] == "0" ) { $b4yf_test_mobi = "0"; }
  $b4yf_wildcard = str_replace("{mobile-test}", $b4yf_test_mobi, $b4yf_wildcard);



/**
 * POST Parameter Übergabe per Formular (Button)
 */
  $b4yf_post_mobi = "";
  if ( isset($_POST['phpContactMobile']) && $_POST['phpContactMobile'] == "1" ) { $b4yf_post_mobi = "1"; }
  if ( isset($_POST['phpContactMobile']) && $_POST['phpContactMobile'] == "0" ) { $b4yf_post_mobi = "0"; }
  if ( isset($_POST['phpContactGetMobile']) && $_POST['phpContactGetMobile'] == _B4YF_BUTTON_MOBILE ) { $b4yf_post_mobi = "1"; }
  if ( isset($_POST['phpContactGetMobile']) && $_POST['phpContactGetMobile'] == _B4YF_BUTTON_DESKTOP ) { $b4yf_post_mobi = "0"; }
  $b4yf_wildcard = str_replace("{mobile}", $b4yf_post_mobi, $b4yf_wildcard);


/**
 * mobil? ja oder nein - für weitere Anwendung
 */
  $phpContact_mobile_theme = "off";
  if ( (defined('_B4YF_MOBILECHECK')) || ($b4yf_post_mobi == "1") || ($b4yf_test_mobi == "1") ) {
    $phpContact_mobile_theme = "on";
  }

?>