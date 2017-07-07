<?php
/**
 * @version   v1.0 - 22.03.2013
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



/**
 * Joomla? 
 */
  if ( defined( '_JEXEC' ) )  { // JA

  /**
   * Userdaten auslesen
   */
    $phpContact_joomla_user =& JFactory::getUser();
    
    $phpContact_joomla_guest = $phpContact_joomla_user->guest; // die Variable $guest liefert den Wert 1, wenn der User ein unbekannter Gast ist. Der Wert 0 bestätigt, das es sich um einen eingeloggten registrierten User handelt.
    $phpContact_joomla_id = $phpContact_joomla_user->id; // ID um den User eindeutig zu identifizieren
    $phpContact_joomla_name = $phpContact_joomla_user->name; // echter Name
    $phpContact_joomla_username = $phpContact_joomla_user->username; // Nickname
    $phpContact_joomla_email = $phpContact_joomla_user->email; // E-Mail Adresse
    $phpContact_joomla_sendemail = $phpContact_joomla_user->sendEmail; // möchte der User System E-Mails erhalten
    $phpContact_joomla_register = $phpContact_joomla_user->registerDate; // Registrierungszeitpunkt
    $phpContact_joomla_lastvisit = $phpContact_joomla_user->lastvisitDate; // Zeitpunkt des letzten angemeldeten Besuches
    $phpContact_joomla_block = $phpContact_joomla_user->block; // User gesperrt oder nicht
    
  }
  
?>