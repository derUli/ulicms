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
 * spezielle Datei für die Integration in das CMS Joomla!
 * Joomla! Messageposition wird für alle Notice und Alert Austgaben verwendet
 * die phpContact Melungen werden unterdrückt
 *
 * Keine Rückgabewerte
 *
 * per Parameter oder in der settings.php werden 2 Variablen an diese Datei übergeben
 *  - $phpContact_joomla
 *  - $phpContact_joomla_message
 */




// Kein direkter Zugang
  defined( '_VALID_B4YF' ) or die ( 'Restricted access' );


// Parameter laden
  $b4yf_template_parameter = $b4yf_absolute_path.'includes/paramsget-variables.inc.php';
  if (file_exists($b4yf_template_parameter)) { require($b4yf_template_parameter); }


/**
 * Informationen aus der Template "settings.php" auslesen
 * $phpContact_joomla_message liefert die Information ob die Joomla Message genutzt werden soll (yes) oder die phpContact Message Position (no)
 * wenn keine settings.php vorhanden ist, oder $phpContact_joomla_message nicht definiert ist, dann wir die phpContact Message verwendet 
  */ 
  if (file_exists($b4yf_absolute_path.'templates/'.$b4yf_template.'/php/settings.php')) {
    include ($b4yf_absolute_path.'templates/'.$b4yf_template.'/php/settings.php');
  }  


/**
 * Defaultwert wenn kein Parameter gesetzt wurde
 */
   if (!isset($phpContact_joomla_message)) { $phpContact_joomla_message = "no"; }


/**
 * Joomla? 
 */
  if ( (defined( '_JEXEC' )) && ($phpContact_joomla_message == "yes") )  { // JA

  /**
   * Schleife um die Meldungen nur ein mal anzuzeigen
   */
    global $b4yf_joomlafile_checked;
    if (!isset($b4yf_joomlafile_checked)) $b4yf_joomlafile_checked = 0;
    if ($b4yf_joomlafile_checked == 0) {
      $b4yf_joomlafile_checked = 1;
    
      /**
       * Testmodus
       */
        if ( (isset($b4yf_testmodus)) && ($b4yf_testmodus == "on") ) JError::raiseNotice(100, _B4YF_NOTICE_TESTMODUS);

      /**
       * Notice Message
       */
        if ( (isset($b4yf_notice)) && ($b4yf_notice!="") ) JError::raiseNotice(100, $b4yf_notice);

      /**
       * Erfolgsmeldung
       */
        if ( (isset($b4yf_erfolgreich)) && ($b4yf_erfolgreich == "yes") ) JFactory::getApplication()->enqueueMessage(_B4YF_INFO_ERFOLGREICH);

    } // Ende der Schleife


  /**
   * Error
   */
    if ($b4yf_alert!="") {
      if ($b4yf_func_errormessage == "master") {
        JError::raiseWarning(100, $b4yf_mastererrormessage);
      } else {
        JError::raiseWarning(100, $b4yf_alert);
      }
    }


  /**
   * Reloadsperre
   * Das Reload nicht als Joomla Message ersetzen, da es nicht automatisch ausgeblendet wird !!!
   */
    // if ($b4yf_alert_reload!="") JError::raiseWarning(100, $b4yf_alert_reload);


  /**
   * NONE-Joomla! Messages löschen
   */
    $b4yf_wildcard = str_replace("{Notice}", "", $b4yf_wildcard);
    $b4yf_wildcard = str_replace("{Error}", "", $b4yf_wildcard);
    // $b4yf_wildcard = str_replace("{Error-Reload}", "", $b4yf_wildcard); // Das Reload nicht als Joomla Message ersetzen, da es nicht automatisch ausgeblendet wird
    $b4yf_wildcard = str_replace("{erfolgreich-oder-nicht}", "", $b4yf_wildcard);
    $b4yf_wildcard = str_replace('<a name="message"></a>', "", $b4yf_wildcard);
    $b4yf_wildcard = str_replace("#message", "#system-message", $b4yf_wildcard);
    
    $b4yf_wildcard = str_replace("b4yf_errorfeld", "b4yf_errorfeld required", $b4yf_wildcard);


  /**
   * Userdaten auslesen
   */
    if (file_exists($b4yf_absolute_path.'templates/'.$b4yf_template.'/tng/joomla/user.php')) {
      include ($b4yf_absolute_path.'templates/'.$b4yf_template.'/tng/joomla/user.php');
    }
  
  /**
   * Joomla? ja oder nein - für weitere Anwendung
   */
    $phpContact_joomla_use = "on";
    
  } // Joomla
  
  else // kein Joomla
  
  {
    $b4yf_wildcard = str_replace("{emailcloak=off}", "", $b4yf_wildcard);
    
  /**
   * Joomla? ja oder nein - für weitere Anwendung
   */
    $phpContact_joomla_use = "off";
  }

?>