<?php 
function socialbuttons_render(){      
    $code = file_get_contents(getModulePath("socialbuttons")."code.html");
    $code = utf8_encode($code);
    return $code;
}
?>