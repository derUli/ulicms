<?php header("Content-Type: text/html; charset = utf-8");
?>
<!doctype HTML>
<html>
<head>
<title>UTF-8 Fixer</title
</head>
<body>
<form action="utf8-fixer.php" method="post">
<strong>Broken</strong><br/>
<textarea name="original" cols=80 rows=25></textarea>
<br/><br/>
<strong>Fixed</strong><br/>
<textarea cols=80 rows=25><?php if(isset($_POST["original"])){
include "Encoding.php";
header("Content-Type: text/plain; charset=utf-8");
$file = $_POST["original"];
echo htmlspecialchars(ForceUTF8\Encoding::fixUTF8($file));
}
?></textarea>
<br/><br/>
<input type="submit" value="Fix!">
</form>