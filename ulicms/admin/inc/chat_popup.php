 <div class="Popup">
            <h2>Chat mit <span id="chatTarget"></h2>
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
        $("#chatMessage").val("")
        alert(message);
    }
});

function openChat(name){
    $("#chatTarget").html(name);
    $('.Popup').fadeIn("slow");
    $('#overlay').fadeIn("slow");
    return false;
}

$('.closePopup').live("click", function() {
    $(".Popup").fadeOut("slow");
    $("#overlay").fadeOut("slow");
    return false;
});
</script>