<?php
if (defined ( "_SECURITY" )) {
	include_once "../version.php";
	$version = new ulicms_version ();
	$acl = new ACL ();
	if (! $acl->hasPermission ( "info" )) {
		noperms ();
	} else {
	    $revision_string = null;
		$gitrev_file = ULICMS_ROOT."/.rev";
		if(file_exists($gitrev_file)){
		   $gitrev_data = file_get_contents($gitrev_file);
		   $revision_string = "Revision " . $gitrev_data;
		   
		}
		$admin_logo = getconfig ( "admin_logo" );
		if (! $admin_logo)
			$admin_logo = "gfx/logo.png";
		?>
<p>
	<img src="<?php echo $admin_logo;?>" alt="UliCMS"
		class="responsive-image" />
</p>
<strong>Release <?php
		
		echo cms_version ();
		?> "<?php
		
		echo $version->getVersion ();
		?>"</strong>
<br />

<?php 
if($revision_string){
?>

<pre>
<?php
   echo htmlspecialchars($revision_string);
?></pre>
<?php }?>

<p>
	<a href="http://www.ulicms.de" target="_blank">UliCMS</a> &copy; 2011 -
	<?php cms_release_year();?> by Ulrich Schmidt<br /> <a href="http://www.ckeditor.com"
		target="_blank">CKEditor</a> &copy; 2003 - 2015 by CKSource<br /> <a
		href="http://kcfinder.sunhater.com/" target="_blank">KCFinder</a>
	Copyright ©2010 - 2014 Pavel Tzonkov<br/ > Copyright 2005, 2014 jQuery
	Foundation, Inc. and other contributors <br /> <a
		href="http://codemirror.net/" target="_blank">CodeMirror</a> &copy;
	2014 by Marijn Haverbeke &lt;marijnh@gmail.com&gt; and others <br /> <a
		href="http://www.raymondhill.net/finediff/viewdiff-ex.php"
		target="_blank">PHP Fine Diff</a> Copyright 2011 (c) Raymond Hill
		<br/>
		<a href="http://mobiledetect.net/" target="_blank">Mobile_Detect</a> Copyright &copy; 2011 - 2015 Serban Ghita, Nick Ilyin and contributors.
<br/>
<a href="https://github.com/chrisbliss18/php-ico" target="_blank">PHP ICO - The PHP ICO Generator</a> - Copyright 2011 - 2013 Chris Jean
<br/>


<a href="https://plugins.jquery.com/url/" target="_blank">jQuery URL Plugin</a> Copyright (C) 2011 - 2012 <a href="http://www.websanova.com" target="_blank">Websanova</a>
<br/>
<a href="http://www.phpgangsta.de/2-faktor-authentifizierung-mit-dem-google-authenticator" target="_blank">GoogleAuthenticator</a>
 Copyright (c) 2012, Michael Kliewe All rights reserved.
</p>

<div>
	Icon made by <a href="http://www.freepik.com" title="Freepik">Freepik</a>
	from <a href="http://www.flaticon.com" title="Flaticon">www.flaticon.com</a>
	is licensed under <a href="http://creativecommons.org/licenses/by/3.0/"
		title="Creative Commons BY 3.0">CC BY 3.0</a>
</div>
<div>
	Icon made by <a href="http://www.freepik.com" title="Freepik">Freepik</a>
	from <a href="http://www.flaticon.com" title="Flaticon">www.flaticon.com</a>
	is licensed under <a href="http://creativecommons.org/licenses/by/3.0/"
		title="Creative Commons BY 3.0">CC BY 3.0</a>
</div>
<div>
	Icon made by <a href="http://www.freepik.com" title="Freepik">Freepik</a>
	from <a href="http://www.flaticon.com" title="Flaticon">www.flaticon.com</a>
	is licensed under <a href="http://creativecommons.org/licenses/by/3.0/"
		title="Creative Commons BY 3.0">CC BY 3.0</a>
</div>
<div>
	Icon made by <a href="http://www.google.com" title="Google">Google</a>
	from <a href="http://www.flaticon.com" title="Flaticon">www.flaticon.com</a>
	is licensed under <a href="http://creativecommons.org/licenses/by/3.0/"
		title="Creative Commons BY 3.0">CC BY 3.0</a>
</div>
<div>
	Icon made by <a href="http://www.elegantthemes.com"
		title="Elegant Themes">Elegant Themes</a> from <a
		href="http://www.flaticon.com" title="Flaticon">www.flaticon.com</a>
	is licensed under <a href="http://creativecommons.org/licenses/by/3.0/"
		title="Creative Commons BY 3.0">CC BY 3.0</a>
</div>
<br />

<input type="button" value="UliCMS Portal"
	onclick='window.open("http://www.ulicms.de");' />

<input type="button"
	value="<?php
		
		echo TRANSLATION_LICENSE;
		?>"
	onclick='window.open("license.html");' />

<input type="button" value="Feedback"
	onclick='location.replace("http://www.ulicms.de/?seite=kontakt");'>


<br />



<?php
	}
    }
?>
