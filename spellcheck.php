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
   $text = str_ireplace("Lybien", "Libyen", $text);
   $text = str_ireplace("maniriert", "manieriert", $text);
   $text = str_ireplace("Maschiene", "Maschine", $text);
   $text = str_ireplace("narzistisch", "narzisstisch", $text);
   $text = str_ireplace("nießen", "niesen", $text);
   $text = str_ireplace("Obulus", "Obolus", $text);
   $text = str_ireplace("orginal", "original", $text);
   $text = str_ireplace("Pappenstil", "Pappenstiel", $text);
   $text = str_ireplace("Pavillion", "Pavillon", $text);
   $text = str_ireplace("pieken", "piken", $text);
   $text = str_ireplace("pieksen", "piksen", $text);
   $text = str_ireplace("Probst", "Propst", $text);
   $text = str_ireplace("projezieren", "projizieren", $text);
   $text = str_ireplace("Prophezeihung", "Prophezeiung", $text);
   $text = str_ireplace("Reflektion", "Reflexion", $text);
   $text = str_ireplace("Religiösität", "Religiosität", $text);
   $text = str_ireplace("Reperatur", "Reparatur", $text);
   $text = str_ireplace("Resource", "Ressource", $text);
   $text = str_ireplace("Rückrad", "Rückgrat", $text);
   $text = str_ireplace("Rückgrad", "Rückgrat", $text);
   $text = str_ireplace("Rückrat", "Rückgrat", $text);
   $text = str_ireplace("Rhytmus", "Rhythmus", $text);
   $text = str_ireplace("Rythmus", "Rhythmus", $text);
   $text = str_ireplace("Schärflein", "Scherflein", $text);
   $text = str_ireplace("Schlawittchen", "Schlafittchen", $text);
   $text = str_ireplace("schmiergeln", "schmirgeln", $text);
   $text = str_ireplace("seelig", "selig", $text);
   $text = str_ireplace("seperat", "separat", $text);
   $text = str_ireplace("Seriösität", "Seriosität", $text);
   $text = str_ireplace("skuril", "skurril", $text);
   $text = str_ireplace("skurill", "skurril", $text);
   $text = str_ireplace("Spirenzchen", "Sperenzchen", $text);
   $text = str_ireplace("sponsorn", "sponsern", $text);
   $text = str_ireplace("gesponsort", "gesponsert", $text);
   $text = str_ireplace("Standart", "Standard", $text);
   $text = str_ireplace("Stehgreif", "Stegreif", $text);
   $text = str_ireplace("subsummieren", "subsumieren", $text);
   $text = str_ireplace("Sylvester", "Silvester", $text);
   $text = str_ireplace("Symetrie", "Symmetrie", $text);
   $text = str_ireplace("sympatisch", "sympathisch", $text);
   $text = str_ireplace("Syphon", "Siphon", $text);
   $text = str_ireplace("Terasse", "Terrasse", $text);
   $text = str_ireplace("Terrabyte", "Terabyte", $text);
   $text = str_ireplace("Thermopen", "Thermopane", $text);
   $text = str_ireplace("tollerant", "tolerant", $text);
   $text = str_ireplace("totlangweilig", "todlangweilig", $text);
   $text = str_ireplace("totschick", "todschick", $text);
   $text = str_ireplace("Triologie", "Trilogie", $text);
   $text = str_ireplace("Tryptichon", "Triptychon", $text);
   $text = str_ireplace("Veriss", "Verriss", $text);
   $text = str_ireplace("Verließ", "Verlies", $text);
   $text = str_ireplace("Verwandschaft", "Verwandtschaft", $text);
   $text = str_ireplace("vorraus", "voraus", $text);
   $text = str_ireplace("Webblog", "Weblog", $text);
   $text = str_ireplace("Wehrmutstropfen", "Wermutstropfen", $text);
   $text = str_ireplace("Wehmutstropfen", "Wermutstropfen", $text);
   $text = str_ireplace("wiederspiegeln", "widerspiegeln", $text);
   $text = str_ireplace("Wiedersacher", "Widersacher", $text);


return $text;



}
?>