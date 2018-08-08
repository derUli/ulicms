<?php
$controller = (ModuleHelper::getMainController("log_viewer"));
$logs = $controller->getLogs();
?>
<?php foreach($logs as $dir=>$files){?>
<?php $anchor = "dir-".md5($dir);?>
<h3 id="<?php esc($anchor);?>"><?php esc($dir);?></h3>
<ul>
<?php foreach($files as $file){?>
<?php
        $queryString = ModuleHelper::buildQueryString(array(
            "dir" => $dir,
            "file" => $file
        ), true);
        $url = ModuleHelper::buildAdminURL("log_viewer", $queryString);
        
        ?>
	<li><a href="<?php
        
        echo $url;
        ?>"><?php esc($file);?></a></li>
<?php }?>
</ul>
<?php }?>