<?php
if(!@nclude_once("Mail.php") and !defined("NO_PEAR_MAIL")){
    define("NO_PEAR_MAIL", true);
}

function pear_mail($to,$subject,$message,$headers="") {
     if(defined("NO_PEAR_MAIL")){  
       return false;

    }
    function split_headers( $headers )
    {
        $header_array = array();
        $lines = explode("\n",$headers);
        foreach ($lines as $line) {
            $kv = explode(":",$line,2);
            if (!empty($kv[1])) {
                $header_array[trim($kv[0])] = trim($kv[1]);
            }
        }
        return $header_array;
    }

    $smtp_host = getconfig("smtp_host");
    if(!$smtp_host) 
        $smtp_host = "127.0.0.1";

    $smtp_port = getconfig("smtp_port");
    if(!$smtp_port) 
        $smtp_port = "25";


    $mailer = Mail::factory('smtp',array('host'=>$smtp_host,'port'=>$smtp_port));
    $header_list = split_headers($headers);
    $header_list['Subject'] = $subject;
    return $mailer->send($to,$header_list,$message);
}


function ulicms_mail($to,$subject,$message,$headers=""){
    $mode = getconfig("email_mode");
    if(!$mode)
       $mode = "internal";


    if($mode == "pear_mail")
       return pear_mail($to,$subject,$message);
    else
       return mail($to,$subject,$message);



}