<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta charset="utf-8">
</head>
<body>
	<table border="1">
		<tbody>
	<?php foreach (ViewBag::get("data") as $label => $value) {?>
	  <tr>
				<td><strong><?php esc($label);?></strong></td>
				<td><strong><?php esc($value);?></strong></td>
			</tr>
	  <?php }?>
	
	</tbody>
	</table>
</body>
</html>
