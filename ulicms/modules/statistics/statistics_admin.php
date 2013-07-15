<?php 
define("MODULE_ADMIN_HEADLINE", "Besucherstatistiken");

$required_permission = getconfig("statistics_required_permission");

if($required_permission === false){
   $required_permission = 20;
}

define(MODULE_ADMIN_REQUIRED_PERMISSION, $required_permission);



function statistics_admin(){
  if(!setlocale(LC_TIME, "de_DE")){
     if(!setlocale(LC_TIME, "de_DE.utf8")){
	   setlocale(LC_TIME, "deu");
	 }
  }

  $data = db_query("SELECT * FROM ".tbname("statistics"));

  $visitor_total = mysql_num_rows($data);

  $views_total = 0;

  $gestern  = mktime(0, 0, 0, date("m"), date("d")-1, date("Y"));
  $heute = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
  $morgen = mktime(0, 0, 0, date("m"), date("d")+1, date("Y"));

  $j = date('Y');
  $m = date('m');
  $d = date('d');
  $monatserster = mktime(0,0,0,$m,1,$j);
  $monatsletzter = mktime(0,0,0,$m+1,0,$j);

  $visitors_today = 0;
  $visitors_yesterday = 0;
  $visitors_month = 0;

  while($row = mysql_fetch_object($data)){
     $views_total += $row->views;
     if($row->date >= $heute and $row->date < $morgen){
        $visitors_today += 1;
     }
   
     if($row->date >= $gestern and $row->date < $heute){
       $visitors_yesterday += 1;
     }
   
     if($row->date >= monatserster and $row->date < $monatsletzter){
       $visitors_month  += 1;
      }
  }




?>
<table>
<tr>
<td style="width:200px;">
<strong>Besucher gesamt:</strong></td>
<td style="text-align:right;"><?php echo intval($visitor_total);?>
</td>
</tr>
<tr>
<td style="width:200px;">
<strong>Besucher heute:</strong></td>
<td style="text-align:right;"><?php echo intval($visitors_today);?>
</td>
</tr><tr>
<td style="width:200px;">
<strong>Besucher gestern:</strong></td>
<td style="text-align:right;"><?php echo intval($visitors_yesterday);?>
</td>
</tr><tr>
<td style="width:200px;">
<strong>Besucher im <?php echo strftime("%B", $monatserster);?>:</strong></td>
<td style="text-align:right;"><?php echo intval($visitors_yesterday);?>
</td>
</tr>
<td style="width:200px;">
<strong>Views gesamt:</strong></td>
<td style="text-align:right;"><?php echo intval($views_total);?>
</td>
</tr>
</table>
<?php

}
 
?>