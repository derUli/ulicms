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
    $b4yf_feld1 = ""; $b4yf_feld2 = ""; $b4yf_feld3 = ""; $b4yf_feld45 = ""; $b4yf_feld67 = ""; $b4yf_feld8 = ""; $b4yf_feld9 = ""; $b4yf_feld10 = ""; $b4yf_feld11 = ""; $b4yf_feld12 = "";
    $b4yf_ParamsCode = "";
    $b4yf_countParams = count($b4yf_param);
    $b4yf_count = 1;
    while ($b4yf_count <= $b4yf_countParams) {
      $b4yf_arrayParams[$b4yf_count] = explode('|',$b4yf_param[$b4yf_count]);

      // Variable erkennen und definieren
      if (strstr($b4yf_arrayParams[$b4yf_count][1], 'var:')) {
        $b4yf_variableName = str_replace( "var:", "", $b4yf_arrayParams[$b4yf_count][1] );
        $b4yf_ParamsCode .=  '$'.$b4yf_variableName.'="'.$b4yf_arrayParams[$b4yf_count][3].'";';
      }
      
      // Block 1
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:1') { if ($b4yf_arrayParams[$b4yf_count][3] != "active" ) { $b4yf_feld1 = "active"; } }
      if ($b4yf_feld1=="") { $b4yf_block1 = "display: none; "; } else { $b4yf_block1 = ""; }
      // Block 2
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:2') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld2 = "active"; } }
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:3') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld3 = "active"; } }
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:4') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld4 = "active"; } }
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:5') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld5 = "active"; } }
      if ( ($b4yf_feld2=="") && ($b4yf_feld3=="") && ($b4yf_feld4=="") && ($b4yf_feld5=="") ) { $b4yf_block2 = "display: none; "; } else { $b4yf_block2 = ""; }
      // Block 3
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:6') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld6 = "active"; } }
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:7') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld7 = "active"; } }
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:8') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld8 = "active"; } }
      if ( ($b4yf_feld6=="") && ($b4yf_feld7=="") && ($b4yf_feld8=="") ) { $b4yf_block3 = "display: none; "; } else { $b4yf_block3 = ""; }
      // Block 4
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:9;feld:10') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld9 = "active"; } }
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:11') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld11 = "active"; } }
      if ( ($b4yf_feld9=="") && ($b4yf_feld11=="") ) { $b4yf_block4 = "display: none; "; } else { $b4yf_block4 = ""; }
      // Block 5
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:9;feld:10') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld9 = "active"; } }
      if ($b4yf_feld9=="") { $b4yf_block5 = "display: none; "; } else { $b4yf_block5 = ""; }

      
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
    echo "}\n";
  }

?>

/* Grundeinstellung */
#phpContact {
  font-size: small !important;
  font-size: x-small; /* IE6 */
  font-family: Verdana, Arial, Helvetica, sans-serif;
  text-align: center;
}

/* Rahmen */
#phpContact .b4yf_rahmen {
  width: <?php echo $phpContact_width; ?>;
  <?php if ($phpContact_align=="center") { echo 'margin: 0 auto;'; } else { echo 'margin: 0;'; } ?>
  <?php if ($phpContact_align=="right") { echo "float: right;"; } ?>
  padding: 0;
  text-align: left;
  font-size: small !important;
  font-size: x-small; /* IE6 */
  font-family: Verdana, Arial, Helvetica, sans-serif;
}

/* Überschrift */
#phpContact h1,
#phpContact h2 {
  margin: 0;
  padding: 10px;
  font-size: 1.6em;
}

/* Kopf */
#phpContact #b4yf_head {
  padding: 0;
  margin: 0;
  font-size: 1em;
  font-weight: bold;
}

/* Headertext */
#phpContact .headertext {
  margin: 0 10px 20px 10px;
  padding: 0;
  font-weight: bold;
}

/* Footertext */
#phpContact .footertext {
  margin: 20px 10px 0 10px;
  padding: 0;
}

/* Absatz */
#phpContact p {
  margin: 0;
  padding: 0 10px;
}

/* Bereiche */
#phpContact .b4yf_fieldset {
  margin: 5px 5px;
  padding: 5px;
  overflow: hidden;
}
#phpContact #block1 {
  <?php echo $b4yf_block1; ?>
}
#phpContact #block2 {
  <?php echo $b4yf_block2; ?>
}
#phpContact #block3 {
  <?php echo $b4yf_block3; ?>
}
#phpContact #block4 {
  <?php echo $b4yf_block4; ?>
}
#phpContact #block5 {
  <?php echo $b4yf_block5; ?>
}

/* Legends */
#phpContact legend {
  position: absolute;
  top: -1000em;
  left: -1000em;
}

/* Label - alle Beschreibungstexte der Felder */
#phpContact label {
  font-family: Verdana, Arial, Helvetica, sans-serif;
  font-size: 0.9em;
  margin-top: 2px;
  font-weight: normal;
}

/* einzeilige Texteingabefelder */
#phpContact .b4yf_inputfeld {
  font-family: Verdana, Arial, Helvetica, sans-serif;
  font-size: 0.9em;
  width: 100%;
  margin-bottom: 3px;
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
}

/* select Feld */
#phpContact .b4yf_select {
  font-family: Verdana, Arial, Helvetica, sans-serif;
  font-size: 0.9em;
  width: 100%;
  margin-bottom: 3px;
  margin-right: 80px;
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
}

/* mehrzeilige Texteingabefelder */
#phpContact .b4yf_textarea {
  font-size: 0.8em;
  width: 100%;
  margin-bottom: 5px;
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
}

/* Eingabefeld und "Durchsuchen-Button" für das File-Feld  */
#phpContact .b4yf_filefeld {
  font-size: 0.8em;
}

/* Eingabefeld für das Captcha */
#phpContact .b4yf_captcha-input {
  width: 100px;
}

/* Captcha */
#phpContact .b4yf_captcha {
  width: 270px !important;
  width: 280px; /* IE6 */
  margin: 15px auto !important;
  margin: 15px 45px; /* IE6 */
  /* padding: 5px 5px 5px 80px; */
  padding: 5px 10px;
  text-align: right;
}

/* Fokus */
#phpContact .b4yf_inputfeld:hover,
#phpContact .b4yf_inputfeld:focus,
#phpContact .b4yf_select:hover,
#phpContact .b4yf_select:focus {
}

/* Errormessage */
#phpContact #b4yf_error {
  margin-bottom: 10px;
  padding: 8px;
  font-weight: bold;
  margin: 5px;
}
#phpContact .b4yf_error {
  text-align: center;
  margin: 11px;
  padding: 5px;
}

/* Noticemessage */
#phpContact #b4yf_notice {
  margin-bottom: 10px;
  padding: 8px;
  font-weight: bold;
  margin: 5px;
}

/* Errormessage and Noticemessage Liste */
#phpContact #b4yf_error ul,
#phpContact #b4yf_notice ul {
  margin: 0 15px;
  padding: 0;
  /* font-weight: normal; */
}
#phpContact #b4yf_error li,
#phpContact #b4yf_notice li {
  margin: 0;
}

/* Infoanzeige "TESTMODUS" */
#phpContact .b4yf_testmodus {
  margin-bottom: 10px;
  padding: 8px;
  text-align: center;
  font-weight: bold;
  margin: 0 0 10px 0;
}

/* Infoanzeige */
#phpContact .b4yf_info {
  margin-bottom: 10px;
  padding: 8px;
  font-weight: bold;
  margin: 0;
}

/* Copyright - MUSS SO GEWÄHLT WERDEN DAS DER TEXT GUT SICHTBAR IST !!! */
#phpContact .b4yf_copyright {
  display: block;
  margin: 0 auto;
  padding: 3px;
  text-align: center;
  font-family: Verdana, Arial, Helvetica, sans-serif;
  font-size: 0.8em;
}

/* Buttons */
#phpContact .b4yf_button {
  width: 150px;
  text-align: center;
  font-family: Verdana, Arial, Helvetica, sans-serif;
  font-size: 1.0em;
}
#phpContact .b4yf_buttonfeld {
  margin: 20px 0;
}

/* Links */
#phpContact a.b4yf:link {
  text-decoration : none;
}
#phpContact a.b4yf:visited {
  text-decoration : none;
}
#phpContact a.b4yf:hover {
  text-decoration : underline;
}
#phpContact a.b4yf:active {
  text-decoration : none;
}

/* Positionierungen */
#phpContact .b4yf_align_right {
  text-align: right;
}
#phpContact .b4yf_align_center {
  text-align: center;
}

/* Eingabefelder als Errorfeld markieren */
#phpContact .b4yf_errorfeld {
}