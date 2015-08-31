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
        $form = self::getFormByID($id);
		throw new NotImplementedException("Forms not implemented yet.");
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
		   $html .= "</body>";
		   $html .= "</html>";
		   
		}
   }
}