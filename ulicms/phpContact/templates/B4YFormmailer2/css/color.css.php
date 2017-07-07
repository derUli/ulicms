<?php
/**
 * @version   v2.5 - 24.05.2013
 * @package   phpContact 1.3.0 - Template: B4YFormmailer2
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
    echo "  background-color: ".$phpContact_color_bg_page.";\n";
    echo "  color: #000;\n";
    echo "}\n";
  }

?>

/* Grundeinstellung */
#phpContact { 
}

/* Rahmen */
#phpContact .b4yf_rahmen {
  border : 1px <?php echo $phpContact_border_form; ?> <?php echo $phpContact_color_border_form; ?>;
  color: <?php echo $phpContact_color_texte ?>;
  background-color: <?php echo $phpContact_color_bg_form; ?>; /* #F5F5F5 */
}

/* Überschrift */
#phpContact h1,
#phpContact h2 {
  color: <?php echo $phpContact_color_h; ?>; /* #EF8700 */
}

/* Kopf */
#phpContact #b4yf_head {
}

/* Absatz */
#phpContact p {
}

/* Bereiche */
#phpContact .b4yf_fieldset {
  border: 1px <?php echo $phpContact_border_fieldset; ?> <?php echo $phpContact_color_border_fieldset; ?>;
  background-color: <?php echo $phpContact_color_bg_fieldset; ?>; /* #F5F5F5 */
}
#phpContact .b4yf_block {
}
#phpContact .b4yf_none {
}

/* Legends */
#phpContact legend {
}

/* Label - alle Beschreibungstexte der Felder */
#phpContact label {
}

/* einzeilige Texteingabefelder */
#phpContact .b4yf_inputfeld {
  color: #333;
  background-color: <?php echo $phpContact_color_bg_inputs; ?>;
  border: 1px solid <?php echo $phpContact_color_border_inputs; ?>;
}

/* select Feld */
#phpContact .b4yf_select {
  color: #333;
  background-color: <?php echo $phpContact_color_bg_inputs; ?>;
  border: 1px solid <?php echo $phpContact_color_border_inputs; ?>;
}

/* mehrzeilige Texteingabefelder */
#phpContact .b4yf_textarea {
  color: #333;
  background-color: <?php echo $phpContact_color_bg_inputs; ?>;
  border: 1px solid <?php echo $phpContact_color_border_inputs; ?>;
}

/* Eingabefeld und "Durchsuchen-Button" für das File-Feld  */
#phpContact .b4yf_filefeld {
}

/* Eingabefeld für das Captcha */
#phpContact .b4yf_captcha-input {
}

/* Captcha */
#phpContact .b4yf_captcha {
  border: 1px <?php echo $phpContact_border_captcha; ?> <?php echo $phpContact_color_border_captcha; ?>;
  background-color: <?php echo $phpContact_color_bg_captcha; ?>;
  background-image : url(../images/security.png);
  background-repeat : no-repeat;
  background-position: 12px 15px;
}

/* Fokus */
#phpContact .b4yf_inputfeld:hover,
#phpContact .b4yf_inputfeld:focus,
#phpContact .b4yf_select:hover,
#phpContact .b4yf_select:focus {
  border: solid 1px <?php echo $phpContact_color_border_inputs_focus; ?>;
}

/* Errormessage */
#phpContact #b4yf_error {
  color: #F00;
}
#phpContact .b4yf_error {
  color: #F00;
  font-weight: bold;
  /* border: 1px solid #AAA; */
  /* background-color: #F88; */
}

/* Noticemessage */
#phpContact #b4yf_notice {
  color: #F48F17;
  /* background-color: #F0E6BE;
  border: 1px solid #FFAD22; */
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
  color: #D14421; /* #F48F17; */
  background-color: #FFFF66; /* #F0E6BE; */
  border: 2px solid #D14421; /* #FFAD22; */
}

/* Infoanzeige */
#phpContact .b4yf_info {
  color: #0055BB;
  background-color: #C3D2E5;
  border: 1px solid #84A7DB;
}

/* Copyright - MUSS SO GEWÄHLT WERDEN DAS DER TEXT GUT SICHTBAR IST !!! */
#phpContact .b4yf_copyright {
  /* border-top: 1px <?php echo $phpContact_border_form; ?> <?php echo $phpContact_color_border_form; ?>; */
}

/* Buttons */
#phpContact .b4yf_button {
}

/* Links */
#phpContact a.b4yf:link {
  color: <?php echo $phpContact_color_texte ?>;
  background-color: transparent;
}
#phpContact a.b4yf:visited {
  color: <?php echo $phpContact_color_texte ?>;
  background-color: transparent;
}
#phpContact a.b4yf:hover {
  color: <?php echo $phpContact_color_texte ?>;
  background-color: transparent;
}
#phpContact a.b4yf:active {
  color: <?php echo $phpContact_color_texte ?>;
  background-color: transparent;
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