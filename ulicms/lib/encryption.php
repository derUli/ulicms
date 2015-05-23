<?php
// Hash Salt + Passwort with SHA1
function hash_password($password){
     $salt = getconfig ("password_salt");
    
     // if no salt is set, generate it
    if (! $salt){
         $newSalt = uniqid ();
         setconfig ("password_salt", $newSalt);
         $salt = $newSalt;
         }
    
     return hash ("sha512", $salt . $password);
    }

?>