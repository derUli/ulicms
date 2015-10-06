function scrollToAnchor(aid) {
	var aTag = $("a[name='" + aid + "']");
	$('html,body').animate({
		scrollTop : aTag.offset().top
	}, 'slow');
}

function shake(div){                                                                                                                                                                                            
    var interval = 100;                                                                                                 
    var distance = 10;                                                                                                  
    var times = 4;                                                                                                      

    $(div).css('position','relative');                                                                                  

    for(var iter=0;iter<(times+1);iter++){                                                                              
        $(div).animate({ left: ((iter%2==0 ? distance : distance*-1))},interval);                                   
    }//for                                                                                                              

    $(div).animate({ left: 0},interval);                                                                                

}//shake   
