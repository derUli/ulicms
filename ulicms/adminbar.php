<?php 
if(defined("ULICMS_ROOT")){
?>

<div class="navbar_top">
<ul class="menu">
  <li>
    <a href='admin/'>Ins Backend wechseln</a>
    </li>
  <li>
    <a href='<?php echo buildSEOUrl();?>?destroy_session=do'>Logout</a>
    </li>
    </ul>
    </div>
<?php }?>