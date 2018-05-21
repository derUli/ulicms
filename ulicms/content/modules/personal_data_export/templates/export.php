<?php
use UliCMS\HTML as HTML;
$person = ViewBag::get("person");
?>
<!DOCTYPE html>
<html>
<head>
<title><?php get_translation("data_export");?></title>
<meta charset="UTF-8">
<style type="text/css">
body {
	font-family: 'wf_SegoeUI', 'Segoe UI', 'Segoe', 'Segoe WP', 'Tahoma',
		'Verdana', 'Arial', 'sans-serif';
	font-size: 16px;
	color: #3B393A;
}

table {
	border-collapse: collapse;
}

table tr td:first-child {
	background-color: rgb(230, 230, 230);
}

table tr td:last-child {
	background-color: rgb(250, 250, 250);
}

table, th, td {
	border: 1px solid black;
}
</style>
</head>
<body>
	<h1><?php translate("data_export")?></h1>
<?php
foreach ($person as $data) {
    $blocks = $data->blocks;
    foreach ($blocks as $block) {
        ?>
    <h2><?php esc($block->title);?></h2>
	<table>
		<tbody>
            	
            <?php
            foreach ($block->blockData as $key => $value) {
            ?>
    <tr>
				<td><strong><?php esc($key);?></strong></td>
				<td><?php echo HTML\text($value);?></td>
			</tr>
    <?php
        }
        ?>
            
       
    </tbody>
	</table>
    
<?php
    }
}
?>
</body>
</html>