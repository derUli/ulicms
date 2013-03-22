<?php 
function getSubscribers(){
   $adresses = array();
   $query = mysql_query("SELECT email FROM ".tbname("newsletter_subscribers"). " ORDER by email ASC");
   
   if(!$query){
     return $adresses;   
   }

   if(mysql_num_rows($query) > 0){
      while($row = mysql_fetch_assoc($query)){
        array_push($adresses, $row["email"]);
      }
   }

    return $adresses;   

}
?>