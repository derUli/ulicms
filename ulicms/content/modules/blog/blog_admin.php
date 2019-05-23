<?php
define ( "MODULE_ADMIN_HEADLINE", "Einstellungen des Blogmoduls" );
define ( "MODULE_ADMIN_REQUIRED_PERMISSION", "blog_settings" );
function blog_admin() {
	if (isset ( $_POST ["submit"] )) {
		
		if ($_POST ["blog_send_comments_via_email"] == "yes") {
			setconfig ( "blog_send_comments_via_email", "yes" );
		} else {
			setconfig ( "blog_send_comments_via_email", "no" );
		}
		if (intval ( $_POST ["blog_feed_max_items"] ) > 0) {
			setconfig ( "blog_feed_max_items", intval ( $_POST ["blog_feed_max_items"] ) );
		}
		
		if (intval ( $_POST ["blog_posts_per_page"] ) > 0) {
			setconfig ( "blog_posts_per_page", intval ( $_POST ["blog_posts_per_page"] ) );
		}
		
		if (isset ( $_POST ["blog_autor_and_date_text"] )) {
			setconfig ( "blog_autor_and_date_text", db_escape ( $_POST ["blog_autor_and_date_text"] ) );
		}
	}
	
	// Konfiguration checken
	$send_comments_via_email = getconfig ( "blog_send_comments_via_email" ) == "yes";
	
	$blog_feed_max_items = getconfig ( "blog_feed_max_items" );
	if ($blog_feed_max_items === false) {
		setconfig ( "blog_feed_max_items", "10" );
		$blog_feed_max_items = 10;
	}
	
	$blog_posts_per_page = getconfig ( "blog_posts_per_page" );
	
	if (! $blog_posts_per_page) {
		$blog_posts_per_page = 5;
	}
	
	$blog_autor_and_date_text = getconfig ( "blog_autor_and_date_text" );
	
	?>

<form action="<?php echo getModuleAdminSelfPath()?>" method="post">
<?php
	
csrf_token_html ();
	?>
<p>
		<input type="checkbox" name="blog_send_comments_via_email" value="yes"
			<?php
	
if (getconfig ( "blog_send_comments_via_email" ) == "yes") {
		echo " checked";
	}
	?> /> Über neue Kommentare per E-Mail benachrichtigen
	</p>
	<p>
		Artikel pro Seite <input type="number" name="blog_posts_per_page"
			size=3 maxlength=3 min="5" max="100"
			value="<?php
	
echo $blog_posts_per_page;
	?>" />
	</p>
	<p>
		maximale Anzahl der Einträge im Newsfeed <input type="number"
			name="blog_feed_max_items" size=3 maxlength=3 min="5" max="100"
			value="<?php
	
echo $blog_feed_max_items;
	?>" />
	</p>

	<p>
		Autor-Text<br /> <textarea name="blog_autor_and_date_text" rows=3
			cols=50><?php
	
echo htmlspecialchars ( $blog_autor_and_date_text, ENT_QUOTES, "UTF-8" );
	?></textarea>
	</p>


	<p>
		<input type="submit" name="submit" value="Einstellungen speichern" />
	</p>
</form>
<?php
     }

?>
