<?php
function kontaktformular_render(){
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

	//Spamschutz
	if($_POST["email"]!=""){
		$fehler = "Das Spamschutz-Feld bitte leer lassen.";
	}
	
	if($fehler==false){
		$headers="From: ".$_POST['emailadresse']."\nReply-To: ".$_POST['emailadresse']."\nContent-Type: text/plain; charset=UTF-8";
		$betreff="Kontaktformular (".env("homepage_title").")";
		$mailtext="--------------------------------------------------------\n".
		"Kontaktformular (".env("homepage_title").")\n".
		"--------------------------------------------------------\n".
		"Vorname:      ".$_POST["vorname"]."\n".
		"Nachname:     ".$_POST["nachname"]."\n".
		"Emailadresse: ".$_POST["emailadresse"]."\n".
		"--------------------------------------------------------\n".
		"Betreff:      ".$_POST["betreff"]."\n".
		"-----------------------------\n".
		"Nachricht:\n\n".$_POST["nachricht"];
	

		if(@mail(env("email"),$betreff,$mailtext,$headers)){
			return "<p class='contactform-success'>Vielen Dank für Ihre Email.<br/>Wir werden diese schnellstmöglich beantworten.</p>";
		}else{
			return "<p class='contactform-error'>Aufgrund technischer Probleme konnte Ihre Email nicht abgeschickt werden. Bitte wenden Sie sich direkt an uns.</p>";
		}


	}else{
		return "<p class='contactform-error'>".$fehler."</p>";
	}

	
	
	}
else{


	return '<form action="'.htmlspecialchars($_SERVER['REQUEST_URI']).'" method="post">
	<table border="0" cellpadding="1" cellspacing="1" style="height: 479px; width: 100%; ">
		<tbody>
			<tr>
				<td>
					<strong>Ihr Vorname: </strong></td>
				<td>
					<input name="vorname" size="40" type="text" /></td>
			</tr>
			<tr>
				<td>
					<strong>Ihr Nachname:</strong></td>
				<td>
					<input name="nachname" size="40" type="text" /></td>
			</tr>
			<tr>
				<td>
					<strong>Ihre Emailadresse</strong>:</td>
				<td>
					<input name="emailadresse" size="40" type="text" /></td>
			</tr>
			<tr>
				<td>
					<strong>Ihr Betreff:</strong></td>
				<td>
					<input name="betreff" size="40" type="text" /></td>
			</tr>
			<tr>
				<td>
					<strong>Spamschutz bitte leer lassen:</strong></td>
				<td>
					<input name="email" size="40" type="text" /></td>
			</tr>
			<tr>
				<td>
					<strong>Ihre Nachricht:</strong></td>
				<td>
					<p>
						<textarea cols="60" name="nachricht" rows="20"></textarea></p>
					<p>
						&nbsp;</p>
				</td>
			</tr>
			<tr>
				<td>
					<strong>Formular:</strong></td>
				<td>
					<input type="reset" value="Zurücksetzen" />&nbsp;&nbsp; <input type="submit" value="Jetzt absenden" /> <input name="absenden" type="hidden" value="absenden" /></td>
			</tr>
			<tr>
				<td>
					&nbsp;</td>
				<td>
					&nbsp;</td>
			</tr>
		</tbody>
	</table>
</form>
<p>
	&nbsp;</p>
';

}



	
}
?>