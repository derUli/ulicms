<?php 
$status = getconfig("blog2twitter_status");

if($status !== false) {
?>

<h2 class="accordion-header">blog2twitter Status</h2>
<div class="accordion-content"><p><?php echo nl2br($status);?></p>
</div>
<?php
}
?>