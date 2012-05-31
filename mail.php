<?php require_once "init.php";
$fehler=false;
if(isset($_POST["absenden"])){

if(empty($_POST["vorname"])){
$fehler="Bitte geben Sie Ihren Vornamen ein.";
}

if(empty($_POST["nachname"])){
$fehler="Bitte geben Sie Ihren Nachnamen ein.";
}

if(empty($_POST["emailadresse"])){
$fehler="Bitte geben Sie Ihren Emailadresse ein, da wir Ihre Mail sonst nicht beantworten können.";
}

if(empty($_POST["betreff"])){
$fehler="Bitte geben Sie einen Betreff ein.";
}

if(empty($_POST["nachricht"])){
$fehler="Sie haben keine Nachricht eingegeben.";
}


}
else{
$fehler="Diese Seite ist zum Absenden des Kontaktformular und nicht zum einzelnen Aufruf gedacht.";
}

header("Content-Type: text/html; charset=UTF-8");

?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo env("homepage_title");?> - Kontakformular</title>
</head>
<body>
<h2>Kontaktformular</h2>
<?php 
if($fehler==false){
$headers="From: ".$_POST['emailadresse']."\nReply-To: ".$_POST['emailadresse']."\nContent-Type: text/plain; charset=UTF-8";
$betreff="Kontaktformular (".env("homepage_title").")";
$mailtext="--------------------------------------------------------
Kontaktformular (".env("homepage_title").")
--------------------------------------------------------
Vorname:      ".$_POST["vorname"]."
Nachname:     ".$_POST["nachname"]."
Emailadresse: ".$_POST["emailadresse"]."
--------------------------------------------------------
Betreff:      ".$_POST["betreff"]."
-----------------------------
Nachricht:

".$_POST["nachricht"];


if(mail(env("email"),$betreff,$mailtext,$headers)){
echo "Vielen Dank für Ihre Email.<br/>Wir werden diese schnellstmöglich beantworten.";
}else{
echo "Aufgrund technischer Probleme konnte Ihre Email nicht abgeschickt werden. Bitte wenden Sie sich direkt an uns.";
}


}else{
echo $fehler;
}
?>
<br/><br/>
<a href="./">Zurück zur Startseite</a>
</body>
</html>