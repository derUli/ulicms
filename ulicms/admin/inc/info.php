<?php if(defined("_SECURITY")){
     include_once "../version.php";
     $version = new ulicms_version();
     ?>
<h4>UliCMS</h4>
<small>Release <?php echo $version -> getVersion();
     ?> (v<?php echo implode(".", $version -> getInternalVersion());
     ?>)</small>
<br/>

<p>
<a href="http://www.deruli.de" target="_blank">UliCMS</a> &copy; 2010 - 2014 by Ulrich Schmidt<br/>
<a href="http://www.ckeditor.com" target="_blank">CKEditor</a> &copy; 2003 - 2013 by CKSource<br/>
<a href="http://kcfinder.sunhater.com/" target="_blank">KCFinder</a> Copyright 2010 - 2012 KCFinder Project   <br/ >
<a href="http://jquery.org" target="_blank">jQuery</a> (c) 2005, 2012 jQuery Foundation, Inc. | jquery.org/license
<br/>

<a href="http://codemirror.net/" target="_blank">CodeMirror</a> &copy; 2012 by Marijn Haverbeke</p>
</p>
<br/>
<?php if(is_admin()){?>
<h2>Technische Informationen</h2>
<table>
<tr>
<td style="width:200px;">
<strong>PHP-Version: </strong></td>
<td>
<?php
echo phpversion();
?>
</td>
</tr>

<tr>
<td style="width:200px;">
<strong>MySQL-Version: </strong></td>
<td>
<?php
echo mysql_get_server_info();
?>
</td>
</tr>
<?php 
if(function_exists("gd_info")){
?>
<tr>
<td style="width:200px;">
<strong>libgd Version: </strong></td>
<td>
<?php
$info = gd_info();
echo $info["GD Version"];
?>
</td>
</tr>
<?php 

}
if(defined("PHP_OS")){
?>
<tr>
<td style="width:200px;">
<strong>Server Betriebssystem: </strong></td>
<td>
<?php
echo PHP_OS;
?>
</td>
</tr>
<?php } else { ?>
<tr>
<td style="width:200px;">
<strong>libgd Version: </strong></td>
<td>
<span style="color:red">libgd ist nicht installiert!<br/>Wahrscheinlich werden Sie keine Bilder hochladen können.</span>
</td>
</tr>
<?php }?>
<?php if(function_exists("apache_get_version")){?>
<tr>
<td style="width:200px;">
<strong>Apache Server Version: </strong></td>
<td>
<?php
echo apache_get_version();
?>
</td>
</tr>
<?php 
} else {
?>
<tr>
<td style="width:200px;">
<strong>Webserver Software: </strong></td>
<td>
<?php
echo $_SERVER["SERVER_SOFTWARE"];
?>
</td>
</tr>

<?php } ?>

<tr>
<td style="width:200px;">
<strong>User-Agent: </strong></td>
<td>
<?php
echo $_SERVER["HTTP_USER_AGENT"];
?>
</td>
</tr>
</table>
<?php 
if(function_exists("apache_get_modules")){
$modules = apache_get_modules();
?>
<h2>Installierte Apache Module</h2>
<?php if(count($modules) > 0 ){?>
<ol>
<?php for($i = 0; $i < count($modules); $i++){?>
<li><?php echo $modules[$i];?></li>
<?php }?>
<?php ?>
</ol>
<?php } else { ?>
<p>Es sind keine Apache Module installiert.</p>
<?php 
}

}?>

<?php }

?><br/>

<input type="button" value="UliCMS Portal" onclick='window.open("http://www.ulicms.de");'/>

<input type="button" value="Lizenz" onclick='window.open("license.html");'/>

<input type="button" value="Feedback" onclick='location.replace("http://www.ulicms.de/?seite=kontakt");'>
<br/>



<?php }
?>
