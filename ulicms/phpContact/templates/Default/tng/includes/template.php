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
 * replaces for the template engine
 */



/**
 * Kein direkter Zugang
 */
  defined( '_VALID_B4YF' ) or die( 'Restricted access' );



/**
 * mobile
 */
  include ($b4yf_absolute_path.'templates/'.$b4yf_template.'/tng/mobile/index.php');
  include_once ($b4yf_absolute_path.'templates/'.$b4yf_template.'/tng/mobile/switch.php');



/**
 * Joomla!
 */
  include_once ($b4yf_absolute_path.'templates/'.$b4yf_template.'/tng/joomla/index.php');



/**
 * replaces laden (wenn vorhanden)
 */
  if (file_exists($b4yf_absolute_path.'templates/'.$b4yf_template.'/php/replaces.php')) {
    include($b4yf_absolute_path.'templates/'.$b4yf_template.'/php/replaces.php');
  }



/**
 * Zeit bis die Reload-Sperre wieder aufgehoben wird kann hier manipuliert werden
 * eventuell mit einem JavaScript Code um einen Countdown anzuzeigen
 * die Variable "$b4yf_time_to_unlock" liefert die Zeit in Sekunden bis die Sperre wieder aufgehoben ist
 * im Template bzw. in der Sprachdatei ist das "Wildcard" %%TIME-TO-UNLOCK%% das durch den Inhalt
 * der Variable ersetzt wird
 */
  if ( isset($b4yf_time_to_unlock) && ($b4yf_time_to_unlock=="") ) {
    $b4yf_time_to_unlock=0;
  }



/**
 * Erfolgsmeldung dynamisch auswählen
 * die Variable "$b4yf_erfolgreich" liefert ein "yes" wenn beim Versenden der Mail kein Fehler geliefert wurde
 * die Variable "$b4yf_testmodus" hat den Wert "on", wenn der Testmodus aktiv ist
 * die Variable "$b4yf_thxmessage" liefert den Bestätigungstext, der in der Konfiguration definiert wurde und ersetzt somit die Konstante "_B4YF_NOTICE_ERFOLGSMELDUNG_YES"
 *
 * Da die Erfolgsmeldung aus der config.php beim Einbau in Joomla nicht funktioniert, muss die config.php hier neu geladen werden
 * Wenn es eine spezielle Konfiguration des aktuellen Templates gibt soll es geladen werden
 */
  $b4yf_config_template = $b4yf_absolute_path.'templates/'.$b4yf_template.'/php/config.php';
  if (file_exists($b4yf_config_template)) {
    require($b4yf_config_template);
  } else {
    $b4yf_template1 = $b4yf_template;
    require( $b4yf_absolute_path.'/config.php' );
    $b4yf_template = $b4yf_template1;
  }

  if ( (isset($b4yf_erfolgreich) && ($b4yf_erfolgreich=="yes")) || (isset($b4yf_testmodus) && ($b4yf_testmodus=="on")) ) {
    $b4yf_erfolgsmeldung  = $b4yf_thxmessage;
  } else {
    $b4yf_erfolgsmeldung  = _B4YF_NOTICE_ERFOLGSMELDUNG_NO;
  }
  $b4yf_wildcard = str_replace("%%ERFOLGSMELDUNG%%", $b4yf_erfolgsmeldung, $b4yf_wildcard);



/**
 * Wildcards auslesen und automatisch durch die Sprachkonstanten ersetzen
 */
  $b4yf_tnc_file = join ('', file ($b4yf_absolute_path.'templates/'.$b4yf_template.'/htmls/formular.tpl.htm'));
  preg_match_all("#%%(.*?)%%#s", $b4yf_tnc_file, $b4yf_tnc_wildcards);
  foreach($b4yf_tnc_wildcards[1] as $b4yf_tnc_wildcard) {
    if ( ($b4yf_tnc_wildcard != "COPYRIGHT") && // Copyright nicht rauslöschen
         ($b4yf_tnc_wildcard != "TIME-TO-UNLOCK") ) { // Reloadsperre nicht rauslöschen
      if ( defined( "_B4YF_".$b4yf_tnc_wildcard ) ) {
        $b4yf_wildcard = str_replace("%%".$b4yf_tnc_wildcard."%%", constant("_B4YF_".$b4yf_tnc_wildcard), $b4yf_wildcard);
      }
      // if ($b4yf_tnc_wildcard=="BUTTON_SENDEN") echo "<h1>".constant("_B4YF_".$b4yf_tnc_wildcard)."</h1>"; // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    }
  }



/**
 * Joomla! Daten Replaces
 */
   if (!isset($phpContact_joomla_guest)) { $phpContact_joomla_guest= ""; }
   if (!isset($phpContact_joomla_id)) { $phpContact_joomla_id= ""; }
   if (!isset($phpContact_joomla_name)) { $phpContact_joomla_name = ""; }
   if (!isset($phpContact_joomla_username)) { $phpContact_joomla_username = ""; }
   if (!isset($phpContact_joomla_email)) { $phpContact_joomla_email = ""; }
   if (!isset($phpContact_joomla_sendemail)) { $phpContact_joomla_sendemail = ""; }
   if (!isset($phpContact_joomla_register)) { $phpContact_joomla_register = ""; }
   if (!isset($phpContact_joomla_lastvisit)) { $phpContact_joomla_lastvisit = ""; }
   if (!isset($phpContact_joomla_block)) { $phpContact_joomla_block = ""; }

   $b4yf_wildcard = str_replace("§§joomla_guest§§", $phpContact_joomla_guest, $b4yf_wildcard);
   $b4yf_wildcard = str_replace("§§joomla_id§§", $phpContact_joomla_id, $b4yf_wildcard);
   $b4yf_wildcard = str_replace("§§joomla_name§§", $phpContact_joomla_name, $b4yf_wildcard);
   $b4yf_wildcard = str_replace("§§joomla_username§§", $phpContact_joomla_username, $b4yf_wildcard);
   $b4yf_wildcard = str_replace("§§joomla_email§§", $phpContact_joomla_email, $b4yf_wildcard);
   $b4yf_wildcard = str_replace("§§joomla_sendemail§§", $phpContact_joomla_sendemail, $b4yf_wildcard);
   $b4yf_wildcard = str_replace("§§joomla_register§§", $phpContact_joomla_register, $b4yf_wildcard);
   $b4yf_wildcard = str_replace("§§joomla_lastvisit§§", $phpContact_joomla_lastvisit, $b4yf_wildcard);
   $b4yf_wildcard = str_replace("§§joomla_block§§", $phpContact_joomla_block, $b4yf_wildcard);

?>