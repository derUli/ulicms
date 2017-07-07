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
 * templatespezifische message.php laden (wenn vorhanden)
 */
  if (file_exists($b4yf_absolute_path.'templates/'.$b4yf_template.'/php/messages.php')) {
    include($b4yf_absolute_path.'templates/'.$b4yf_template.'/php/messages.php');
  }



/**
 * Wenn das Formular aus irgend einem Grund nicht gesendet wurde, werden alle File-Felder geleert
 * Das ist eine Sicherheitsfunktion der Browser und kann nicht geändert werden
 * Aus diesem Grund wird eine entsprechende Meldung angezeigt
 */
  if (defined( '_B4YF_SEND_ATTACHMENT' )) {
    // $b4yf_alert  .= '<li>'._B4YF_ERROR_ATTACHMENT.'</li>';
    $b4yf_notice .= '<li>'._B4YF_ERROR_ATTACHMENT.'</li>';
  }

?>