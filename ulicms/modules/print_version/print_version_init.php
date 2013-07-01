<?php 
if(isset($_REQUEST["print"])){
include_once "templating.php";
?>
<html>
<head>
<script type="text/javascript">
window.onload = function(){
   window.print();
}
</script>
<style type="text/javascript">
a, a:hover, a:visited{
color:blue;
}
</style>
<title><?php title(); ?></title>
<?php 
base_metas();
?>
</head>
<body>
<h1><?php title();?></h1>

<?php 
   content();

?>
</body>
</html>
<?php die(); 
}
?>