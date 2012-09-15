<?php 
function facebook_feed_render(){

	ob_start();
	
    if(!getconfig("facebook_feed_jquery_enabled")){
       setconfig("facebook_feed_jquery_enabled", "yes");
    }
    if(getconfig("facebook_id") === ""){
		echo '<div class="ulicms_error">Die Facebook-ID wurde noch nicht gesetzt.<br/>
		Deshalb kann diese Seite nicht geöffnet werden.</div>';
		return ob_get_clean();
	
	}
	
	if(!getconfig("facebook_feed_width")){
		setconfig("facebook_feed_width", "458");
	}
	
	if(getconfig("facebook_feed_jquery_enabled") === "yes"){
		echo '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>';
	}
	

    echo'
<script type="text/javascript" src="http://social-extension.abouttheweb.de/external/jquery.ba-postmessage.js"></script>
<script type="text/javascript" src="http://social-extension.abouttheweb.de/external/abouttheweb-fb-feed.js"></script>
<atw_stream width="'.getconfig("facebook_feed_width").'" page="'.getconfig("facebook_id").'" filter="1" lang="de" />
';



	return ob_get_clean();
}

?>