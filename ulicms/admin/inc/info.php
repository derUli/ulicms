<?php if(defined("_SECURITY")){
     include_once "../version.php";
     $version = new ulicms_version();
     $acl = new ACL();
     if(!$acl -> hasPermission("info")){
         noperms();
         }else{
         ?>
<p><img src="gfx/logo.png" alt="UliCMS"/></p>
<strong>Release <?php echo cms_version();
         ?> "<?php echo $version -> getVersion();
         ?>"</strong>
<br/>

<p>
<a href="http://www.ulicms.de" target="_blank">UliCMS</a> &copy; 2010 - 2015 by Ulrich Schmidt<br/>
<a href="http://www.ckeditor.com" target="_blank">CKEditor</a> &copy; 2003 - 2015 by CKSource<br/>
<a href="http://kcfinder.sunhater.com/" target="_blank">KCFinder</a> Copyright 2010 - 2012 KCFinder Project   <br/ >
Copyright 2005, 2014 jQuery Foundation, Inc. and other contributors
<br/>
<a href="http://codemirror.net/" target="_blank">CodeMirror</a> &copy; 2014 by Marijn Haverbeke &lt;marijnh@gmail.com&gt; and others</p>

<div>Icon made by <a href="http://www.freepik.com" title="Freepik">Freepik</a> from <a href="http://www.flaticon.com" title="Flaticon">www.flaticon.com</a> is licensed under <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0">CC BY 3.0</a></div>
<div>Icon made by <a href="http://www.freepik.com" title="Freepik">Freepik</a> from <a href="http://www.flaticon.com" title="Flaticon">www.flaticon.com</a> is licensed under <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0">CC BY 3.0</a></div>
<div>Icon made by <a href="http://www.freepik.com" title="Freepik">Freepik</a> from <a href="http://www.flaticon.com" title="Flaticon">www.flaticon.com</a> is licensed under <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0">CC BY 3.0</a></div>
<div>Icon made by <a href="http://www.google.com" title="Google">Google</a> from <a href="http://www.flaticon.com" title="Flaticon">www.flaticon.com</a> is licensed under <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0">CC BY 3.0</a></div>
<br/>

<input type="button" value="UliCMS Portal" onclick='window.open("http://www.ulicms.de");'/>

<input type="button" value="<?php echo TRANSLATION_LICENSE;
         ?>" onclick='window.open("license.html");'/>


<input type="button" value="Community" onclick='location.replace("http://www.ulicms.de/forum.html");'>

<input type="button" value="Feedback" onclick='location.replace("http://www.ulicms.de/?seite=kontakt");'>

 
<br/>



<?php }
    
     }
?>
