<?php
/**
 * @version   v4.5 - 22.05.2013
 * @package   phpContact 1.3.0 - Template: Default
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
      
      // Eingabefelder aller persönlichen Daten deaktiviert? Variable $b4yf_persoenliches_block definieren
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:1') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld1 = "active"; } }
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:2') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld2 = "active"; } }
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:3') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld3 = "active"; } }
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:4;feld:5') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld45 = "active"; } }
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:6;feld:7') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld67 = "active"; } }
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:8') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld8 = "active"; } }
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:9') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld9 = "active"; } }
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:10') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld10 = "active"; } }
      if ( ($b4yf_feld1=="") && ($b4yf_feld2=="") && ($b4yf_feld3=="") && ($b4yf_feld45=="") && ($b4yf_feld67=="") && ($b4yf_feld8=="") && ($b4yf_feld9=="") && ($b4yf_feld10=="") ) { 
        $b4yf_persoenliches_block = "display: none; "; } else { $b4yf_persoenliches_block = "";
      }

      // Eingabefelder aller persönlichen Daten deaktiviert? Variable $b4yf_persoenliches_block definieren
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:11') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld11 = "active"; } }
      if ($b4yf_arrayParams[$b4yf_count][1] == 'feld:12') { if ($b4yf_arrayParams[$b4yf_count][3] == "active" ) { $b4yf_feld12 = "active"; } }
      if ( ($b4yf_feld11=="") && ($b4yf_feld12=="") ) { 
        $b4yf_nachricht_block = "display: none; "; } else { $b4yf_nachricht_block = "";
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
  if ($b4yf_work == "self") { 
    echo "body {\n";
    echo "font-size: small;\n";
    echo "margin: 0;\n";
    echo "padding: 10px;\n";
    echo "}\n";
  }
  
?>

/* Grundeinstellung */
#phpContact {
  font-size: small;
  font-family: Verdana, Arial, Helvetica, sans-serif;
}

/* IE6 fix */
* html #phpContact {
   font-size: smaller;
}

/* Überschrift */
#phpContact h1 {
  font-family: Georgia, Times, Serif;
  padding: 10px;
  font-size: 1.5em;
  font-weight: bold;
  margin: 0;
}
#phpContact h2 {
  font-family: Georgia, Times, serif;
  font-size: 1.3em;
  margin: 5px 0 15px 0; /* oben, rechts, unten, links */
  padding: 2px;
  text-align: left;
}

/* Logo */
#phpContact .b4yf_logo {
  height: 65px;
  <?php if ($phpContact_logo=="nein") { echo 'display: none;'; } ?>
}

/* Absatz */
#phpContact .p {
  padding: 0 10px 10px 10px;
  margin: 0;
}

/* Rahmen */
#phpContact .b4yf_rahmen {
  width: <?php echo $phpContact_width; ?>;
  <?php if ($phpContact_align=="center") { echo 'margin: 0 auto;'; } else { echo 'margin: 0;'; } ?>
  <?php if ($phpContact_align=="right") { echo "float: right;"; } ?>
  padding: 0;
}

/* Bereiche */
#phpContact .b4yf_fieldset {
  margin: 5px 5px;
  padding: 5px;
}
#phpContact #b4yf_persoenliches p{
  width: 365px;
  margin: 0 auto;
}
#phpContact #b4yf_persoenliches {
<?php echo $b4yf_persoenliches_block; ?>
}
#phpContact #b4yf_nachricht {
<?php echo $b4yf_nachricht_block; ?>
}
#phpContact #b4yf_captcha {
<?php echo $b4yf_captcha_block; ?>
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
}

/* Label - für den Bereich "persönliches" */
#phpContact #b4yf_persoenliches label {
  width: 130px;
  float: left;
  text-align: right;
  margin-right: 5px;
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

* + html #phpContact #b4yf_nachricht .b4yf_inputfeld { /* IE7 Hack */
  margin-left: -5px;
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
  width: 98px;
}

/* Captcha */
#phpContact .b4yf_captcha {
  width: 170px;
  margin: 5px auto;
  padding: 5px;
  text-align: right;
  border: 1px solid #aaa;
  background-color: #fff;
  background-image : url(../images/lock.png);
  background-repeat : no-repeat;
  background-position: 2px 2px;
  height: 56px;
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
  margin: 5px;
}

/* Noticemessage */
#phpContact #b4yf_notice {
  margin-bottom: 10px;
  padding: 8px;
  padding-left: 40px;
  font-weight: bold;
  margin: 5px;
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
  margin: 5px;
}

/* Infoanzeige */
#phpContact .b4yf_info {
  margin-bottom: 10px;
  padding: 8px;
  padding-left: 40px;
  font-weight: bold;
  margin: 5px;
}

/* Copyright - MUSS SO GEWÄHLT WERDEN DAS DER TEXT GUT SICHTBAR IST !!! */
#phpContact .b4yf_copyright {
  display: block;
  width: 100%;
  margin: 0;
  padding: 3px 0;
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

/* spezielle Formatierungen der Eingabefelder */
#phpContact #b4yf_persoenliches input {
  width: 220px;
  float: left;
}
#phpContact #b4yf_persoenliches #strasse {
  width: 170px;
}
#phpContact #b4yf_persoenliches #nr {
  width: 47px;
  margin-left: 3px;
}
#phpContact #b4yf_persoenliches #plz {
  width: 47px;
}
#phpContact #b4yf_persoenliches #ort {
  width: 170px;
  margin-left: 3px;
}