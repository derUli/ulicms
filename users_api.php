<?php 
// this class contains functions for managing user accounts


function getUsers(){
  $query = mysql_query("SELECT * FROM ".tbname("admins")." ORDER by username");
  $users = Array();
  while($row = mysql_fetch_object($query)){
    array_push($users, $row->username);
  }
  
  return $users;
}

function getUserByName($name){
  $query = mysql_query("SELECT * FROM ".tbname("admins"). " WHERE username='".
  mysql_real_escape_string($name)."'");
  if(mysql_num_rows($query)>0){
    return mysql_fetch_assoc($query);
  }else{
    return false;
  }
}

function getUserById($id){
  $query = mysql_query("SELECT * FROM ".tbname("admins"). " WHERE id = ".intval($id));
  if(mysql_num_rows($query)>0){
    return mysql_fetch_assoc($query);
  }else{
    return false;
  }
}


function adduser($username, $lastname, $firstname, $email, $password, $group){
  $username = mysql_real_escape_string($username);
  $lastname = mysql_real_escape_string($lastname);
  $firstname = mysql_real_escape_string($firstname);
  $email = mysql_real_escape_string($email);
  $password = mysql_real_escape_string($password);
  $group = intval($group);

  $query=mysql_query("INSERT INTO ".tbname("admins")." 
(username,lastname, firstname, email, password, `group`) VALUES('$username',' $lastname','$firstname','$email','".md5($password)."',$group)");
  $message="Hallo $firstname,\n\n".
  "Ein Administrator hat auf http://".$_SERVER["SERVER_NAME"]." für dich ein neues Benutzerkonto angelegt.\n\n".
  "Die Zugangsdaten lauten:\n\n".
  "Benutzername: $username\n".
  "Passwort: $password\n";
  $header="From: ".getconfig("email")."\n".
  "Content-type: text/plain; charset=utf-8";

  @mail($email, "Dein Benutzer-Account bei ".$_SERVER["SERVER_NAME"], $message,     $header);
}

function user_exists($name){
  $query = mysql_query("SELECT * FROM ".tbname("admins").
  " WHERE username = '".mysql_real_escape_string($name)."'");
  return mysql_num_rows($query) > 0;
}
?>