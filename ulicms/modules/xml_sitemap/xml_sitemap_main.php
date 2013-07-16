<?php 
function xml_sitemap_render(){
   if(is_file("sitemap.xml")){
      return "<a href\"sitemap.xml\">XML Sitemap</a>";
   }
   else {
      return "sitemap.xml wurde noch nicht generiert.";
   }
}
?>