<?php 
header("Content-Type: text/html; charset = utf-8");
include "Encoding.php";
?>
<!doctype HTML>
<html>
<head>
<title>UTF-8 Fixer</title
</head>
<body>
<form action="utf8-fixer.php" method="post">
<strong>Broken</strong><br/>
<textarea name="original" cols=80 rows=25><?php if(isset($_POST["original"])) echo htmlspecialchars($_POST["original"]);?></textarea>
<br/><br/>
<strong>Fixed</strong><br/>
<textarea cols=80 rows=25><?php if(isset($_POST["original"])){
$file = $_POST["original"];
echo htmlspecialchars(ForceUTF8\Encoding::fixUTF8($file));
}
?></textarea>
<br/><br/>
<input type="submit" value="Fix!">
</form>