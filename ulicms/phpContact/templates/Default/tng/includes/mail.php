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
 * replaces for the mail template engine
 * Achtung: Diese Datei wird mehrfach geladen. Vorsicht bei der Definition von Funktionen!
 */



/**
 * Kein direkter Zugang
 */
  defined( '_VALID_B4YF' ) or die( 'Restricted access' );



/**
 * alle verfügbaren Variablen aus der Parameterdatei auslesen
 * notwendig für die Replaces der E-Mail
 */
  $b4yf_params_template = $b4yf_absolute_path.'templates/'.$b4yf_template.'/php/params.php';
  if (file_exists($b4yf_params_template)) {
    include($b4yf_params_template);
    $b4yf_ParamsCode = "";
    $b4yf_countParams = count($b4yf_param);
    $b4yf_count = 1;
    while ($b4yf_count <= $b4yf_countParams) {
      $b4yf_arrayParams[$b4yf_count] = explode('|',$b4yf_param[$b4yf_count]);
      if (strstr($b4yf_arrayParams[$b4yf_count][1], 'var:')) {
        $b4yf_variableName = str_replace( "var:", "", $b4yf_arrayParams[$b4yf_count][1] );
        $b4yf_ParamsCode .=  '$'.$b4yf_variableName.'="'.$b4yf_arrayParams[$b4yf_count][3].'";';
      }
      $b4yf_count++;
    }
    eval($b4yf_ParamsCode);
  }



/**
 * Wildcards auslesen und automatisch durch die Sprachkonstanten ersetzen
 */
  $b4yf_tnc_file = join ('', file ($b4yf_absolute_path.'templates/'.$b4yf_template.'/htmls/mail.tpl.htm'));
  $b4yf_tnc_file .= join ('', file ($b4yf_absolute_path.'templates/'.$b4yf_template.'/htmls/formular.tpl.htm'));
  preg_match_all("#%%(.*?)%%#s", $b4yf_tnc_file, $b4yf_tnc_wildcards1); // Konstanten
  preg_match_all("/##(.*?)##/s", $b4yf_tnc_file, $b4yf_tnc_wildcards2); // Variablen

  // Kontstanten
  foreach($b4yf_tnc_wildcards1[1] as $b4yf_tnc_wildcard) {
    if ( $b4yf_tnc_wildcard != "COPYRIGHT" ) {  // Copyright nicht rauslöschen
      @$b4yf_wildcard = str_replace("%%".$b4yf_tnc_wildcard."%%", constant("_B4YF_".$b4yf_tnc_wildcard), $b4yf_wildcard);
    }
  }
  
  // Variablen
  foreach($b4yf_tnc_wildcards2[1] as $b4yf_tnc_wildcard) {
    $b4yf_tnc_wildcard2 = "phpContact_".$b4yf_tnc_wildcard;
    @$b4yf_wildcard = str_replace("##".$b4yf_tnc_wildcard."##", $$b4yf_tnc_wildcard2, $b4yf_wildcard);
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



/**
 * Autoresponder
 */
  $b4yf_wildcard = str_replace("{autorespondermessage-html}", $b4yf_autorespondermessage, $b4yf_wildcard);
  $b4yf_wildcard = str_replace("{autorespondermessage-text}", strip_tags($b4yf_autorespondermessage), $b4yf_wildcard);

?>