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
 * any useful functions
 */



/**
 * Kein direkter Zugang
 */
  defined( '_VALID_B4YF' ) or die( 'Restricted access' );



/**
 * Funktion zur Berechnung der MB bzw. KB aus den Bytes.
 * Zurückgeliefert wird eine "schöne" Anzeige mit Einheit.
 * Zuvor wird noch die Existenz der Funktion überpfüft.
 */
  if ( !function_exists('byte_umrechner')) {
    function byte_umrechner($bytes) {
      if ($bytes > pow(2,10)) {
        if ($bytes > pow(2,20)) {
          $size = number_format(($bytes / pow(2,20)), 2);
          $size .= " MB";
          return $size;
        } else {
          $size = number_format(($bytes / pow(2,10)), 2);
          $size .= " KB";
          return $size;
        }
      } else {
        $size = (string) $bytes . " Bytes";
        return $size;
      }
    }
  }

?>