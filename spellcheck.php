<?php 

/* Häufige Rechtschreibfehler korrigieren
   Basiert auf folgenden Wikipedia-Artikel
   http://de.wikipedia.org/wiki/Liste_h%C3%A4ufiger_Rechtschreibfehler_im_Deutschen
   
*/
function autocorrect_common_typos($text){
   $text = str_ireplace("a capella", "a cappella", $text);
   $text = str_ireplace("Abberation", "Aberration", $text);
   $text = str_ireplace("agressiv", "aggressiv", $text);
   $text = str_ireplace("Akkustik", "Akustik", $text);
   $text = str_ireplace("Amalgan", "Amalgam", $text);
   $text = str_ireplace("Amaturenbrett", "Armaturenbrett", $text);
   $text = str_ireplace("assozial", "asozial", $text);
   $text = str_ireplace("Atrappe", "Attrappe", $text);
   $text = str_ireplace("authorisiert", "autorisiert", $text);
   $text = str_ireplace("Authorisierung", "Autorisierung", $text);
   $text = str_ireplace("Besenreißer", "Besenreiser", $text);
   $text = str_ireplace("Billiard", "Billard", $text);
   $text = str_ireplace("bischen", "bisschen", $text);
   $text = str_ireplace("Bisquit", "Biskuit", $text);
   $text = str_ireplace("bombadieren", "bombardieren", $text);
   $text = str_ireplace("brilliant", "brillant", $text);
   $text = str_ireplace("Camenbert", "Camembert", $text);
   $text = str_ireplace("Capuccino", "Cappuccino", $text);
   $text = str_ireplace("detailiert", "detailliert", $text);
   $text = str_ireplace("dilletantisch", "dilettantisch", $text);
   $text = str_ireplace("Diphterie", "Diphtherie", $text);
   $text = str_ireplace("Eigenbrödler", "Eigenbrötler", $text);
   $text = str_ireplace("einzelnd", "einzeln", $text);
   $text = str_ireplace("Entgeld", "Entgelt", $text);
   $text = str_ireplace("entgültig", "endgültig", $text);
   $text = str_ireplace("Extase", "Ekstase", $text);$text = str_ireplace("fröhnen", "frönen", $text);
   $text = str_ireplace("Gallerie", "Galerie", $text);
   $text = str_ireplace("Gallionsfigur", "Galionsfigur", $text);
   $text = str_ireplace("Gebahren", "Gebaren", $text);
   $text = str_ireplace("Gelantine", "Gelatine", $text);
   $text = str_ireplace("gesäht", "gesät", $text);
   $text = str_ireplace("gothisch", "gotisch", $text);
   $text = str_ireplace("Gradwanderung", "Gratwanderung", $text);
   $text = str_ireplace("Gries", "Grieß", $text);
   $text = str_ireplace("gröhlen", "grölen", $text);
   $text = str_ireplace("hälst", "hältst", $text);
   $text = str_ireplace("Imbus", "Inbus", $text);
   $text = str_ireplace("Imission", "Immission", $text);
   $text = str_ireplace("Ingredenzien", "Ingredienzien", $text);
   $text = str_ireplace("Kandarre", "Kandare", $text);
   $text = str_ireplace("kommisarisch", "kommissarisch", $text);
   $text = str_ireplace("Kriese", "Krise", $text);
   $text = str_ireplace("Lapalie", "Lappalie", $text);
   $text = str_ireplace("Lilliputaner", "Liliputaner", $text);
   $text = str_ireplace("lizensieren", "lizenzieren", $text);
   $text = str_ireplace("Lizensierung", "Lizenzierung", $text);
   $text = str_ireplace("", "", $text);
   $text = str_ireplace("", "", $text);
   $text = str_ireplace("", "", $text);
   $text = str_ireplace("", "", $text);
   $text = str_ireplace("", "", $text);
   $text = str_ireplace("", "", $text);
   $text = str_ireplace("", "", $text);
   $text = str_ireplace("", "", $text);


return $text;



}
?>