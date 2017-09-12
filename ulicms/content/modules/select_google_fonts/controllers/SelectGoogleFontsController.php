<?php
class SelectGoogleFontsController extends Controller {
  private $moduleName = "select_google_fonts";
   public function adminHead(){
        // @FIXME: Das hier darf nur eingebunden werden, wenn man sich
        // auf der SettingsPage des Moduls befindet.
        echo Template::executeModuleTemplate($this->moduleName, "head.php");
   }
}
