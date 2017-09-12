<?php
class SelectGoogleFontsController extends Controller {
  private $moduleName = "select_google_fonts";
   public function adminHead(){
        if(Request::getVar("action") == "module_settings" and
           Request::getVar("module") == $this->moduleName){
          echo Template::executeModuleTemplate($this->moduleName, "head.php");
        }
   }
   public function settings(){
        echo Template::executeModuleTemplate($this->moduleName, "settings.php");
   }
}
