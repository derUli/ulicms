<?php
class VongConverter extends Controller{
   // Todo: Übersetzung soll seperat für Frontend und Backend
   // eingeschalten und deaktiviert werden können.
   public function frontendFooter(){
     if(getCurrentLanguage() !== "de"){
       return;
     }
      return Template::executeModuleTemplate("vong_converter", "vong.php");
  }
  public function adminFooter(){
    if(getSystemLanguage() !== "de"){
      return;
    }
    return $this->frontendFooter();
  }
}
