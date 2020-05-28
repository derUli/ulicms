<?php 
    $controller = new SimpleSettingsController ();
    $timezones = $controller->getTimezones();
?>

<ul>
<?php foreach($timezones as $timezone){ ?>
<li><?php esc($timezone);?></li>
<?php } ?>
</ul>
