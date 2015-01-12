<?php if(defined("_SECURITY")){
     include_once "../version.php";
     $version = new ulicms_version();
     $acl = new ACL();
     if(!$acl -> hasPermission("info")){
         noperms();
         }else{
         ?>
<p><img src="gfx/logo.png" alt="UliCMS"/></p>
<strong>Release <?php echo implode(".", $version -> getInternalVersion());
         ?> "<?php echo $version -> getVersion();
        ?>"</strong>
<br/>

<p>
<a href="http://www.deruli.de" target="_blank">UliCMS</a> &copy; 2010 - 2014 by Ulrich Schmidt<br/>
<a href="http://www.ckeditor.com" target="_blank">CKEditor</a> &copy; 2003 - 2014 by CKSource<br/>
<a href="http://kcfinder.sunhater.com/" target="_blank">KCFinder</a> Copyright 2010 - 2012 KCFinder Project   <br/ >
<a href="http://jquery.org" target="_blank">jQuery</a> (c) 2005, 2012 jQuery Foundation, Inc.
<br/>

<a href="http://codemirror.net/" target="_blank">CodeMirror</a> &copy; 2012 by Marijn Haverbeke</p>
</p>

<input type="button" value="UliCMS Portal" onclick='window.open("http://www.ulicms.de");'/>

<input type="button" value="<?php echo TRANSLATION_LICENSE;
        ?>" onclick='window.open("license.html");'/>


<input type="button" value="Community" onclick='location.replace("http://www.ulicms.de/forum.html");'>

<input type="button" value="Feedback" onclick='location.replace("http://www.ulicms.de/?seite=kontakt");'>

 
<br/>



<?php }
    
     }
?>
