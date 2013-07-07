 <div class="Popup">
            <h1>Popupcontent</h1>
            <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr.</p>
            <a href="#" class="closePopup">close</a>
        </div>
        <div id="overlay" class="closePopup"></div>


<script type="text/javascript">
function openChat(name){
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