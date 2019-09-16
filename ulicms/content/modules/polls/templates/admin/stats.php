<?php
include_once getModulePath("polls", true) . "/main.php";
$id = intval($_REQUEST["poll_stats"]);
$question = new Question($id);
if ($question->getID() === null) {
    translate("poll_not_found");
} else {
    
    $votes_total = PollFactory::getVotesSum($id);
    ?>
<!doctype html>
<html>
<head>
<meta name="viewport" content="width=device-width, user-scalable=yes" />
<title><?php
    
    Template::escape($question->title);
    ?></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<?php
    $enq = array(
        "scripts/php.js/strip_tags.js",
        "scripts/php.js/htmlspecialchars.js",
        "scripts/jquery.min.js",
        "scripts/jquery.form.min.js",
        "scripts/jquery.tablesorter.min.js",
        "scripts/vallenato/vallenato.js",
        "codemirror/lib/codemirror.js",
        "codemirror/mode/php/php.js",
        "codemirror/mode/xml/xml.js",
        "codemirror/mode/javascript/javascript.js",
        "codemirror/mode/clike/clike.js",
        "codemirror/mode/css/css.js",
        "scripts/url.min.js",
        "scripts/util.js"
    );
    
    foreach ($enq as $script) {
        enqueueScriptFile($script);
    }
    ?>

<?php combinedScriptHtml();?>
<link rel="stylesheet" type="text/css" href="" />
<?php
    $styles = array();
    $styles[] = "css/blue.css";
    $styles[] = getModulePath("polls") . "css/stats.css";
    
    foreach ($styles as $style) {
        enqueueStylesheet($style);
    }
    
    combined_stylesheet_html();
    ?>
<link rel="stylesheet" type="text/css"
	href="scripts/tablesorter/style.css" />
<link rel="icon" href="gfx/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="gfx/favicon.ico" type="image/x-icon" />
<link rel="apple-touch-icon" href="gfx/apple-touch-icon.png" />
<link rel="apple-touch-icon" sizes="57x57"
	href="gfx/apple-touch-icon-57x57.png" />
<link rel="apple-touch-icon" sizes="72x72"
	href="gfx/apple-touch-icon-72x72.png" />
<link rel="apple-touch-icon" sizes="76x76"
	href="gfx/apple-touch-icon-76x76.png" />
<link rel="apple-touch-icon" sizes="114x114"
	href="gfx/apple-touch-icon-114x114.png" />
<link rel="apple-touch-icon" sizes="120x120"
	href="gfx/apple-touch-icon-120x120.png" />
<link rel="apple-touch-icon" sizes="144x144"
	href="gfx/apple-touch-icon-144x144.png" />
<link rel="apple-touch-icon" sizes="152x152"
	href="gfx/apple-touch-icon-152x152.png" />
<script type="text/javascript">
$(document).ready(function(){	
    $(".tablesorter").tablesorter({widgets: ["zebra"]}); 
    } 
); 
</script>
</head>
<body>
	<div class="stats-container">
		<h1><?php
    
    Template::escape($question->title);
    ?></h1>

		<table class="tablesorter">
			<thead>
				<tr>
					<th><?php translate("answer");?></th>

					<th class="votes-row"><?php translate("votes");?></th>
				</tr>
			</thead>
			<tbody>
<?php
    foreach ($question->getAnswers() as $answer) {
        ?>
<tr>
					<td><?php Template::escape($answer->title);?></td>
					<td style="text-align: right" class="votes-row number-font"><?php Template::escape($answer->getVotes());?></td>
				</tr>
<?php }?>
		</tbody>
		</table>

		<table class="votes-total">
			<tr>
				<td><strong><?php translate("votes_total");?></strong></td>
				<td style="text-align: right" class="votes-row"><span
					class="double_underline number-font"><?php echo PollFactory::getVotesSum($question->getID());?></span>
				</td>
			</tr>
		</table>

		<div class="poll-bar-graph no-print"
			style="margin-top: 20px; border: 1px solid #eee; padding: 20px;">
<?php
    foreach ($question->getAnswers() as $answer) {
        $votes = 0.00;
        if ($votes_total > 0 and $answer->getVotes() > 0) {
            $votes = $answer->getVotes() * 100 / $votes_total * 3;
        }
        $votes = str_replace(",", ".", $votes);
        $color = RandomColor::get();
        ?>
			<strong>
			<?php Template::escape($answer->title);?>
			</strong> <br />
			
<?php if($votes > 0){?>
<div style="width: <?php echo intval($votes);?>px; background-color:<?php echo $color;?>; float:left;margin-right:5px;">&nbsp;</div>
<?php
        }
        ?><div style="">
<?php echo $answer->getVotes();?></div>


			<?php
    }
    ?>
		</div>
		<div class="no-print print-button-container">
			<input type="button" value="<?php translate("print_results");?>"
				onclick="window.print();">
		</div>
	</div>
</body>
</html>
<?php }?>