$(function() {
	if(sessionStorage.getItem("openMenu") === null){
		sessionStorage.setItem("openMenu", "yes" );
	}
	if(sessionStorage.getItem("openMenu") == "yes"){
			$(".mainmenu").slideDown();
	}
	$("#menu-toggle").click(function() {
		$(".mainmenu").slideToggle();
			if(sessionStorage.getItem("openMenu") == "yes"){
					sessionStorage.setItem("openMenu", "no" );
				} else {
						sessionStorage.setItem("openMenu", "yes" );
				}
	});
});
