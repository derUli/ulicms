<?php
class VongConverter extends Controller{
   // Todo: Nur deutsch übersetzen
   // Übersetzung soll seperat für Frontend und Backend
   // eingeschalten und deaktiviert werden können.
   public function frontendFooter(){
      return Template::executeModuleTemplate("vong_converter", "vong.php");
  }
  public function adminFooter(){
    return $this->frontendFooter();
  }
}
