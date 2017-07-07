<?php
/**
 * @version   v2.5 - 26.05.2013
 * @package   phpContact 1.3.0 - Template: Dataload
 * @author    Günther Hörandl <info@phpcontact.net>
 * @copyright Copyright (c) 2009 - 2013 by Günther Hörandl
 * @license   http://www.phpcontact.net/license.html, see LICENSE.txt
 * @link      http://www.phpcontact.net/
 */



/**
 * Compression und Header Definitionen
 */
  ob_start ("ob_gzhandler");
  ob_start("compress");
  header("Content-type: text/css;charset: UTF-8");
  header("Cache-Control: must-revalidate");
  $offset = 60 * 60 ;
  $ExpStr = "Expires: " . gmdate("D, d M Y H:i:s",time() + $offset) . " GMT";
  header($ExpStr);
  function compress($buffer) {
    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
    $buffer = str_replace(array("\r\n","\r","\n","\t",'  ','    ','    '),'',$buffer);
    $buffer = str_replace('{ ', '{', $buffer);
    $buffer = str_replace(' }', '}', $buffer);
    $buffer = str_replace('; ', ';', $buffer);
    $buffer = str_replace(', ', ',', $buffer);
    $buffer = str_replace(' {', '{', $buffer);
    $buffer = str_replace('} ', '}', $buffer);
    $buffer = str_replace(': ', ':', $buffer);
    $buffer = str_replace(' ,', ',', $buffer);
    $buffer = str_replace(' ;', ';', $buffer);
    $buffer = str_replace(';}', '}', $buffer);
    return $buffer;
  }



/**
 * alle verfügbaren Variablen aus der Parameterdatei auslesen
 */
  $b4yf_params_template = '../php/params.php';
  if (file_exists($b4yf_params_template)) {
    require_once($b4yf_params_template);
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
 * eigenständige Seite? Ja, dann BODY Style definieren
 */
  define( '_VALID_B4YF', 1 ); // Elterndatei
  include('../../../config.php'); // Konfig laden
  ini_set("error_reporting",E_ALL); // alle Warnungen anzeigen
  if ($b4yf_work == "self") {
    echo "body {\n";
    echo "  background-color: ".$phpContact_page_backgroundcolor.";\n";
    echo "  color: #000;\n";
    echo "}\n";
  }

?>

/* Grundeinstellung */
#phpContact {
}

/* Rahmen */
#phpContact .b4yf_rahmen {
}

/* Überschriften */
#phpContact h1 {
}
#phpContact h2 {
}
#phpContact h3 {
}

/* Absätze */
#phpContact p {
}

/* Listen */
#phpContact  ul {
}
#phpContact li {
}

/* Bereiche */
#phpContact .b4yf_fieldset {
  border: 1px solid <?php echo $phpContact_block_bordercolor; ?>;
  background-color: <?php echo $phpContact_block_backgroundcolor; ?>;
}

/* Titel */
#phpContact .titel {
  color: <?php echo $phpContact_block_backgroundcolor; ?>;
  background-color: <?php echo $phpContact_block_bordercolor; ?>;
}
#phpContact .subtitel {
  background-color: <?php echo $phpContact_block_backgroundcolor; ?>;
  color: #000;
}

/* Legends */
#phpContact #b4yf_persoenliches legend,
#phpContact #b4yf_nachricht legend {
}

/* Label - alle Beschreibungstexte der Felder */
#phpContact label {
}

/* Label - für den Bereich "persönliches" */
#phpContact #b4yf_persoenliches label {
}

/* einzeilige Texteingabefelder */
#phpContact .b4yf_inputfeld {
  color: #333;
  background-color: #FFF;
  border: 1px solid #AAA;
}

/* select Feld */
#phpContact .b4yf_select {
  color: #333;
  background-color: #FFF;
  border: 1px solid #AAA;
}

/* mehrzeilige Texteingabefelder */
#phpContact .b4yf_textarea {
  color: #333;
  background-color: #FFF;
  border: 1px solid #AAA;
}

/* Eingabefeld und "Durchsuchen-Button" für das File-Feld  */
#phpContact .b4yf_filefeld {
}

/* Eingabefeld für das Captcha */
#phpContact .b4yf_captcha-input {
}

/* Captcha */
#phpContact .b4yf_captcha {
  border: 1px solid #999;
  background-color: #fff;
  background-image : url(../images/lock.png);
  background-repeat : no-repeat;
  background-position: 2px 2px;
}

/* Captcha Info */
#phpContact .b4yf_captcha_info {
}

/* Fokus */
#phpContact .b4yf_inputfeld:hover,
#phpContact .b4yf_inputfeld:focus,
#phpContact .b4yf_select:hover,
#phpContact .b4yf_select:focus {
  background-color: #EEE;
  border: solid 1px #999;
}

/* Errormessage */
#phpContact #b4yf_error,
#phpContact .b4yf_error {
  color: #C00;
  background-color: #E6C0C0;
  border-top: 3px solid #DE7A7B;
  border-bottom: 3px solid #DE7A7B;
  background-image : url(../images/error.png);
  background-repeat : no-repeat;
  background-position: 1px 1px;
}

/* Noticemessage */
#phpContact #b4yf_notice {
  color: #F48F17;
  background-color: #F0E6BE;
  border-top: 3px solid #FFAD22;
  border-bottom: 3px solid #FFAD22;
  background-image : url(../images/note.png);
  background-repeat : no-repeat;
  background-position: 1px 1px;
}

/* Errormessage and Noticemessage Liste */
#phpContact #b4yf_error ul,
#phpContact #b4yf_notice ul {
}
#phpContact #b4yf_error li,
#phpContact #b4yf_notice li {
}

/* Infoanzeige "TESTMODUS" */
#phpContact .b4yf_testmodus {
  color: #F48F17;
  background-color: #F0E6BE;
  border-top: 3px solid #FFAD22;
  border-bottom: 3px solid #FFAD22;
  background-image : url(../images/note.png);
  background-repeat : no-repeat;
  background-position: 1px 1px;
}

/* Infoanzeige */
#phpContact .b4yf_info {
  color: #0055BB;
  background-color: #C3D2E5;
  border-top: 3px solid #84A7DB;
  border-bottom: 3px solid #84A7DB;
  background-image : url(../images/info.png);
  background-repeat : no-repeat;
  background-position: 1px 1px;
}

/* Copyright - MUSS SO GEWÄHLT WERDEN DAS DER TEXT GUT SICHTBAR IST !!! */
#phpContact .b4yf_copyright {
}

/* Buttons */
#phpContact .b4yf_button {
}

/* Links */
#phpContact a.b4yf:link {
  color : #333;
  background-color: transparent;
}
#phpContact a.b4yf:visited {
  color : #333;
  background-color: transparent;
}
#phpContact a.b4yf:hover {
  color : #000;
  background-color: transparent;
  text-decoration: underline;
}
#phpContact a.b4yf:active {
  color : #000;
  background-color: transparent;
  text-decoration: underline;
}

/* Positionierungen */
#phpContact .b4yf_align_right {
}
#phpContact .b4yf_align_center {
}

/* Eingabefelder als Errorfeld markieren */
#phpContact .b4yf_errorfeld {
  background-color: #F88;
}

/* Fieldset "Persönliches" */
#phpContact #b4yf_persoenliches {
  /* background-image: url(../images/personen.png);
  background-position: 460px 50px;
  background-repeat: no-repeat; */
}

/* Absatz "Persönliches" und "Dateien" */
#phpContact #b4yf_persoenliches p,
#phpContact #b4yf_dateien p{
}

/* spezielle Formatierungen der Eingabefelder */
#phpContact #b4yf_persoenliches input,
#phpContact #b4yf_dateien input  {
}

/* Ladeanzeige */
#phpContact #loader {
  color: #000;
}

/* hr */
#phpContact hr {
  background-color: #999;
}