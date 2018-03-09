<?php
class PolyfillIo extends MainClass{
    const MODULE_NAME = "polyfill_io";
    public function frontendFooter(){
        return Template::executeModuleTemplate(PolyfillIo::MODULE_NAME, "script.php");
    }
    public function adminFooter(){
        return $this->frontendFooter();
    }

}