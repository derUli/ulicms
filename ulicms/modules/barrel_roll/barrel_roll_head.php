<?php
$modules = getAllModules();
if(in_array("jquery", $modules) and isset($_GET["q"])){
    $q = $_GET["q"];
    $q = trim($q);
    $q = strtolower($q);
    if($q == "do a barrel roll"){
         ?>
<style type="text/css">
.barrel_roll {
    -webkit-transition: -webkit-transform 4s ease;
    -webkit-transform: rotate(360deg);
    -moz-transition: -moz-transform 4s ease;
    -moz-transform: rotate(360deg);
    -o-transition: -o-transform 4s ease;
    -o-transform: rotate(360deg);
    transition: transform 4s ease;
    transform: rotate(360deg);
}  
</style>
<script type="text/javascript">
function barrel_roll() {
    $('body').addClass('barrel_roll');
  setTimeout("$('body').removeClass('barrel_roll')", 4000);
}

window.onload = function(){
   barrel_roll();
};


</script>
<?php
        }
    }
?>