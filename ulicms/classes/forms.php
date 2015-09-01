<?php
class Forms{
   public static function getFormByID($id){
       $retval = null;
	   $query = db_query("select * from `".tbname("forms")."` WHERE id = ".intval($id));
	   if(db_num_rows($query) > 0){
	      $retval = db_fetch_assoc($query);
	   }
	   
	   return $retval;
   }
   
   public static function createForm($name, $email_to, $subject, $category_id, $fields, 
                                     $mail_from_field, $target_page_id){
									 $name = db_escape($name);
									 $email_to = db_escape($email_to);
									 $subject = db_escape($subject);
									 $category_id = intval($category_id);
									 $fields = db_escape($fields);
									 $mail_from_field = db_escape($mail_from_field);
									 $target_page_id = intval($target_page_id);
									 $created = time();
									 $updated = time();
									 
									 return db_query("INSERT INTO `".tbname("forms")."` (name, email_to, subject, category_id, `fields`,
									 mail_from_field, target_page_id, `created`, `updated`) values ('$name', '$email_to', '$subject', $category_id, '$fields',
									 '$mail_from_field', $target_page_id, $created, $updated)");
       
   }
   
   public static function getAllForms(){
       $retval = array();
	   $query = db_query("select * from `".tbname("forms")."` ORDER BY id");
	   if(db_num_rows($query) > 0){
	      while($row = db_fetch_assoc($query)){
		       $retval[] = $row;
		  }
	   }
	   
	   return $retval;
   }
   
   public static function submitForm($id){
        $retval = false;
        $form = self::getFormByID($id);
		if($form){
		   $fields = $form["fields"];
		   $fields = str_replace("\r\n", "\n", $fields);
		   $fields = explode("\n", $fields);
		   $fields = array_map("trim", $fields);
		   $html = "<!DOCTYPE html>";
		   $html .= "<html>";
		   $html .= "<head>";
		   $html .= '<meta http-equiv="content-type" content="text/html; charset=utf-8">';
           $html .= '<meta charset="utf-8">';
		   $html .="</head>";
		   $html .= "<body>";
		   $html .= "<table border=\"1\"";
		   foreach($fields as $field){
		      if(!empty($field)){
		      $field_splitted = explode("=>", $field);
              $field_splitted = array_map("trim", $field_splitted);
			  if(count($field_splitted) > 1){
			      $label = $field_splitted[1];
			  } else{
			      $label = $field_splitted[0];
			  }
			  
			  }
			  
			  
			  $value = "";
			  if(isset($_POST[$field_splitted[0]]) and !empty($_POST[$field_splitted[0]])){
			     $value = $_POST[$field_splitted[0]];
			  }
			  
			  $html .= "<tr>";
			  $html .= "<td><strong>".htmlspecialchars($label)."</strong></td>";
			  $html .= "<td>".htmlspecialchars($value)."</td>";
			  $html .= "</tr>";
		   }
		   $html .="</table>";
		   $html .= "</body>";
		   $html .= "</html>";
		   
		   $email_to = $form["email_to"];
		   $subject = $form["subject"];
		   $target_page_id = $form["target_page_id"];
		   $target_page_systemname = getPageSystemnameByID($target_page_id);
		   $redirect_url = buildSEOUrl($target_page_systemname);
		   
		   $headers = "Content-Type: text/html; charset=UTF-8";
		   
		   $mail_from_field = $form["mail_from_field"];
		   
		   if(!is_null(§mail_from_field) and !empty($mail_from_field) and isset($_POST[$mail_from_field]) and !empty($_POST[$mail_from_field])){
		     $mail_from = $_POST[$mail_from_field];
			 sanitize($mail_from);
			 $headers .="\n";
			 $headers .= "From: ".$mail_from;
		   }
		   
		   if(ulicms_mail($email_to, $subject, $html, $headers)){
		      ulicms_redirect($redirect_url);
			  $retval = true;
		   } else {
		      translate("error_send_mail_form_failed");
			  die();
		}
   }
      return $retval;
   }
   
   
   }
