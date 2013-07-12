<?php
if(in_array("blog", getAllModules()) and has_permissions(40)){
     ?>

<h2 class="accordion-header">Neueste Kommentare</h2>
<div class="accordion-content">
<?php
     if(isset($_GET["delete_comment"]) and $_SESSION["group"] >= 40){
         mysql_query("DELETE FROM " . tbname("blog_comments") . " WHERE id=" . intval($_GET["delete_comment"]));
         }
    
    
     $query = mysql_query("SELECT * FROM " . tbname("blog_comments") . " ORDER by `date` DESC LIMIT 5");
    
     if(mysql_num_rows($query) === 0){
         echo "<p>Es sind noch keine Kommentare vorhanden.</p>";
         }else{
        
         $html = "";
        
         while($comment = mysql_fetch_object($query)){
             $count++;
            
             $html .= "<div class='a_comment'>
	   <a href='#comment" . $comment -> id . "' name='comment" . $comment -> id . "'>";
             $html .= "#" . $count;
            
            
            
            
             $html .= "</a>";
            
             if($_SESSION["group"] >= 40){
                 $html .= " <a href='index.php?delete_comment=" . $comment -> id . "' onclick='return confirm(\"Diesen Kommentar wirklich löschen?\")'>[Löschen]</a>";
                 }
            
             $html .= "<br/>";
             $html .= "<br/>";
             $html .= "<strong>Name: </strong>";
             $html .= $comment -> name;
             $html .= "<br/>";
            
             if($_SESSION["group"] >= 20){
                 $html .= "<strong>Email: </strong>" . $comment -> email . "<br/>";
                 }
            
             if($_SESSION["language"] == "de"){
                 $html .= "<strong>Datum:</strong>";
                
                 }else{
                 $html .= "<strong>Date:</strong>";
                 }
            
            
            
            
             $html .= " ";
             $html .= date(getconfig("date_format"),
                 $comment -> date);
             if($comment -> url != "http://" and $comment -> url != ""){
                 $html .= "<br/>";
                 $html .= "<strong>Homepage:</strong> " . "<a href='" . $comment -> url . "' target='_blank' rel='nofollow'>" . $comment -> url . "</a>";
                 }
             $html .= "<br/><br/>";
             $html .= nl2br(htmlspecialchars($comment -> comment));
            
             $html .= "<br/><br/>";
            
             if($count != mysql_num_rows($query)){
                 $html .= "<hr/>";
                 }
            
            
             $html .= "</div>";
             }
        
         echo $html;
        
         }
     ?>
</div>
<?php
     }



?>
