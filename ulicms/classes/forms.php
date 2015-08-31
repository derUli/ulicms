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
		   $html .= "<table style=\"border:1px; width:100%;\">";
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
