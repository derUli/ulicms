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
  // ob_start ("compress");
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
      
      // Eingabefelder aller persönlichen Daten deaktiviert? Variable $b4yf_persoenliches_block definieren
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:1') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld1 = "active"; } }
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:2') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld2 = "active"; } }
      if ( ($b4yf_feld1=="") && ($b4yf_feld2=="") ) { 
        $b4yf_persoenliches_block = "display: none; "; } else { $b4yf_persoenliches_block = "";
      }

      // Eingabefelder aller persönlichen Daten deaktiviert? Variable $b4yf_persoenliches_block definieren
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:3') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld3 = "active"; } }
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:4') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld4 = "active"; } }
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:5') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld5 = "active"; } }
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:6') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld6 = "active"; } }
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:7') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld7 = "active"; } }
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:8') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld8 = "active"; } }
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:9') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld9 = "active"; } }
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:10') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld10 = "active"; } }
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:11') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld11 = "active"; } }
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:12') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld12 = "active"; } }
      if ( ($b4yf_feld3=="") && ($b4yf_feld4=="") && ($b4yf_feld14=="") && ($b4yf_feld16=="") && ($b4yf_feld7=="") && ($b4yf_feld8=="") && ($b4yf_feld9=="") && ($b4yf_feld10=="") && ($b4yf_feld11=="") && ($b4yf_feld112=="") ) { 
        $b4yf_dateien_block = "display: none; "; } else { $b4yf_dateien_block = "";
      }
     
      // Captach aktiv? Variable $b4yf_captcha_block definieren
      if (strstr($b4yf_arrayParams[$b4yf_count][1], 'feld:13;feld:14')) {
        if ($b4yf_arrayParams[$b4yf_count][3] != "active" ) { $b4yf_captcha_block = "display: none; "; } else { $b4yf_captcha_block = ""; }
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
  if ( (isset($b4yf_work)) && ($b4yf_work == "self") ) { 
    echo "body {\n";
    echo "font-family: Verdana, Arial, Helvetica, sans-serif;\n";
    echo "font-size: small;\n";
    echo "margin: 0;\n";
    echo "padding: 10px;";
    echo "}\n";
  }
  
?>



/* Grundeinstellung */
#phpContact {
  font-family: Verdana, Arial, Helvetica, sans-serif;
  font-size: 1em;
  width: <?php echo $phpContact_width; ?>;
  <?php if ($phpContact_align=="center") { echo 'margin: 0 auto;'; } else { echo 'margin: 0;'; } ?>
  <?php if ($phpContact_align=="right") { echo "float: right;"; } ?>
}

/* IE6 fix */
* html #phpContact {
   font-size: smaller;
}

/* Rahmen */
#phpContact .b4yf_rahmen {
}

/* Überschriften */
#phpContact h1 {
  font-size: 1.8em;
  font-family: trebuchet MS, sans-serif;
}
#phpContact h2 {
  font-size: 1.4em;
  font-family: trebuchet MS, sans-serif;
}
#phpContact h3 {
  font-size: 1.2em;
}

/* Absätze */
#phpContact p {
}

/* Listen */
#phpContact  ul {
  margin: 0 0 0 20px;
  padding: 0;
}
#phpContact li {
  font-size: 0.9em;
  list-style: square;
}

/* Bereiche */
#phpContact .b4yf_fieldset {
  margin: 0 0 20px 0;
  padding: 20px;
}
#phpContact #block1 {
  <?php echo $b4yf_persoenliches_block; ?>
}
#phpContact #block2 {
  <?php echo $b4yf_dateien_block; ?>
}
#phpContact #block3 {
  <?php echo $b4yf_captcha_block; ?>
}

/* Titel */
#phpContact .titel,
#phpContact .subtitel {
  width: 100%;
  text-align: right;
  font-family: trebuchet MS, sans-serif;
  font-size: 1.5em;
  font-weight: bold;
  margin: 0;
  padding: 0;
}
#phpContact .titel span,
#phpContact .subtitel span {
  padding: 0 10px;
}

/* Legends */
#phpContact  legend {
  position: absolute;
  top: -1000em;
  left: -1000em;
}

/* Label - alle Beschreibungstexte der Felder */
#phpContact label {
  font-size: 0.9em;
  margin-top: 2px;
}

/* Label - für den Bereich "persönliches" */
#phpContact #b4yf_persoenliches label ,
#phpContact #b4yf_dateien label  {
  width: 130px;
  float: left;
  text-align: right;
  margin-right: 5px;
}

/* einzeilige Texteingabefelder */
#phpContact .b4yf_inputfeld {
  font-family: Verdana, Arial, Helvetica, sans-serif;
  font-size: 0.9em;
  width: 99%;
  margin-bottom: 3px;
  float: left;
}

/* select Feld */
#phpContact .b4yf_select {
  font-family: Verdana, Arial, Helvetica, sans-serif;
  font-size: 0.9em;
  width: 150px;
  margin-bottom: 3px;
  margin-right: 80px;
  float: left;
}

/* mehrzeilige Texteingabefelder */
#phpContact .b4yf_textarea {
  font-size: 0.8em;
  width: 99%;
  margin-bottom: 5px;
}

/* Eingabefeld und "Durchsuchen-Button" für das File-Feld  */
#phpContact .b4yf_filefeld {
  font-family: Verdana, Arial, Helvetica, sans-serif;
  font-size: 0.9em;
  width: 99%;
  margin-bottom: 3px;
  float: left;
}

/* Eingabefeld für das Captcha */
#phpContact .b4yf_captcha-input {
  width: 98px;
  float: none;
}

/* Captcha */
#phpContact .b4yf_captcha {
  width: 170px;
  margin: 0;
  padding: 5px;
  text-align: right;
  float: left;
}

/* Captcha Info */
#phpContact .b4yf_captcha_info {
  float: left;
  padding-left: 20px;
}

/* Fokus */
#phpContact .b4yf_inputfeld:hover,
#phpContact .b4yf_inputfeld:focus,
#phpContact .b4yf_select:hover,
#phpContact .b4yf_select:focus {
}

/* Errormessage */
#phpContact #b4yf_error,
#phpContact .b4yf_error {
  margin-bottom: 10px;
  padding: 8px;
  padding-left: 40px;
  font-weight: bold;
  margin: 0 0 20px 0;
}

/* Noticemessage */
#phpContact #b4yf_notice {
  margin-bottom: 10px;
  padding: 8px;
  padding-left: 40px;
  font-weight: bold;
  margin: 0 0 20px 0;
}

/* Errormessage and Noticemessage Liste */
#phpContact #b4yf_error ul,
#phpContact #b4yf_notice ul {
  margin: 0 15px;
  padding: 0;
  font-weight: normal;
}
#phpContact #b4yf_error li,
#phpContact #b4yf_notice li {
  margin: 0;
}

/* Infoanzeige "TESTMODUS" */
#phpContact .b4yf_testmodus {
  margin-bottom: 10px;
  padding: 8px;
  padding-left: 40px;
  font-weight: bold;
  margin: 0 0 20px 0;
}

/* Infoanzeige */
#phpContact .b4yf_info {
  margin-bottom: 10px;
  padding: 8px;
  padding-left: 40px;
  font-weight: bold;
  margin: 0 0 20px 0;
}

/* Copyright - MUSS SO GEWÄHLT WERDEN DAS DER TEXT GUT SICHTBAR IST !!! */
#phpContact .b4yf_copyright {
  display: block;
  width: <?php echo $phpContact_width; ?>;
  margin: 0;
  padding: 0;
  text-align: center;
  font-family: Verdana, Arial, Helvetica, sans-serif;
  font-size: 0.8em;
}

/* Buttons */
#phpContact .b4yf_button {
  width: 120px;
  text-align: center;
  font-family: Verdana, Arial, Helvetica, sans-serif;
  font-size: 1.0em;
  margin-top: 0 !important;
  margin-top: 20px;
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

/* Fieldset "Persönliches" */
#phpContact #b4yf_persoenliches {
}

/* Absatz "Persönliches" und "Dateien" */
#phpContact #b4yf_persoenliches p,
#phpContact #b4yf_dateien p{
  width: 380px;
}

/* spezielle Formatierungen der Eingabefelder */
#phpContact #b4yf_persoenliches input,
#phpContact #b4yf_dateien input  {
  width: 220px;
  float: left;
}

/* Ladeanzeige */
#phpContact #loader {
  display: none;
  text-align: center;
  height: 50px;
}

/* Grafik */
#phpContact img.attachment {
  position: absolute;
  left: 450px;
  top: 100px;
  display: none;
}

/* hr */
#phpContact hr {
}

/* Verstecken */
#phpContact .b4yf_none {
  display: none;
}

/* Headertext */
#phpContact .headertext {
  margin: 20px 0;
  padding: 0;
}

/* Footertext */
#phpContact .footertext {
  margin: 20px 0;
  padding: 0;
}