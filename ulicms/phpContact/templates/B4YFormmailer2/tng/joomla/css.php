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
 * diese Datei wird nach der fertigen (!) Generierung des Formulars eingebaut
 *
 * Keine Rückgabewerte
 *
 * per Parameter oder in der settings.php werden 2 Variablen an diese Datei übergeben
 *  - $phpContact_joomla
 *  - $phpContact_joomla_message
 */



// Kein direkter Zugang
  defined( '_VALID_B4YF' ) or die ( 'Restricted access' );


/**
 * Joomla? 
 */
  if ( (defined( '_JEXEC' )) && ($phpContact_joomla=="yes") ) { // JA
  
    // CSS für die Ausgabe hinzufügen
    // "invalid" für nicht oder falsch ausgefülltes Eingabefeld
    // "required" für ein Pflichtfeld

    $b4yf_echo = str_replace("b4yf_errorfeld", "b4yf_errorfeld invalid", $b4yf_echo);
    $b4yf_echo = str_replace("b4yf_required", "b4yf_required required", $b4yf_echo);

  }

?>