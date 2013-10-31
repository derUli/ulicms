<?php 
function xmlrpc_calls_hook_test(){
  return "Diese API-Funktion wurde über die xmlrpc_calls Hook hinzugefügt";
}

register_xmlrpc_call("demo.xmlrpc_calls_hook", "xmlrpc_calls_hook_test");


?>