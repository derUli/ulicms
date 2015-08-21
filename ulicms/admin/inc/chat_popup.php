<?php
if (! defined ( "_SECURITY" )) {
	die ();
}
?>
<div class="Popup">
	<h2>
		Chat mit <span id="chatTarget">
	
	</h2>
	<form action="#">
		<div id="chatLog"></div>
		<input id="chatMessage" value="" type="text">
	</form>

	<a href="#" class="closePopup">Schließen</a>
</div>
<div id="overlay" class="closePopup"></div>


<script type="text/javascript">

$("#chatMessage").keyup(function(event){
    if(event.keyCode == 13){
        var message = $("#chatMessage").val();
        if(message.length < 3){
           alert("Ihre Nachricht ist zu kurz.\nDiese muss mindestens 3 Zeichen enthalten.");
           return;
        }
        $("#chatMessage").val("")
        addMessageToLog("<?php
								
								echo $_SESSION ["ulicms_login"];
								?>", message);
        var data = {"ajax_cmd": "sendChatMessage", 
        "to" : $("#chatTarget").html(),
        "from" : '<?php
								
								echo $_SESSION ["ulicms_login"];
								?>',
        "message" : 'message'
        }
        $.post('index.php', data, function(response) {
         alert(response);
    });

    }
    
    event.preventDefault();
    event.stopPropagation();
});

$(".Popup form").submit(function(event){

    event.preventDefault();
    event.stopPropagation();
});

function addMessageToLog(from, message){
  var message = htmlspecialchars(message, 'ENT_QUOTES');
  var html_string = "<p><strong>" + from + ":</strong> " + message + "</p>";
$("#chatLog").append(html_string)
}



function openChat(name){
    $("#chatTarget").html(name);
    $('.Popup').fadeIn("slow");
    $('#overlay').fadeIn("slow");
    return false;
}

$('.closePopup').click(function() {
    $(".Popup").fadeOut("slow");
    $("#overlay").fadeOut("slow");
    return false;
});
</script>
