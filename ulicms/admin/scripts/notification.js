


$(document).ready(function(e){ 
  old_onlineList = new Array();
  new_onlineList = new Array();
  checkForNewUsersOnline();

});


function showChangedOnlineStatus(name, online){
  $("#message").hide();
 
   if(online){
    $("#message").html("<span style='color:green'>" + name + " ist online</span>");
  } else{
     $("#message").html("<span style='color:red'>" + name + " ist offline</span>");  
  }
  $("#message").fadeIn(400, function(e){
  
    if(online)
      play_sound("sounds/online.mp3");
    else
      play_sound("sounds/offline.mp3");
  setTimeout(function(){
  $("#message").fadeOut(400, function(e){
  $("#message").html("");
  $("#message").show();
  });
  
  }, 8 * 1000)
    
  }
  );

}

function checkForNewUsersOnline(){
    $.ajax({
        type: "POST",
        url: "index.php?ajax_cmd=users_online",
        async: true,
        success : function(e){
              new_onlineList = strip_tags(e);
              new_onlineList = new_onlineList.split("\n");
              new_onlineList = $.grep(new_onlineList, function(n, i){
                 return (n !== "" && n != null);
              });
              
              if(old_onlineList.length != new_onlineList){
              
               for(var i=0; i < new_onlineList.length; i++){
                  var name = new_onlineList[i];
                  if($.inArray( name, old_onlineList) ==-1){
                  
                     if(old_onlineList.length != 0){
                       showChangedOnlineStatus(name, true)
                     }
                  }
                  
              }
              
              
               for(var i=0; i < old_onlineList.length; i++){
                  var name = old_onlineList[i];
                  if($.inArray( name, new_onlineList) ==-1){
                  
                     if(old_onlineList.length != 0){
                       showChangedOnlineStatus(name, false)
                     }
                  }
                  
              }
              
              
                  old_onlineList = new_onlineList;
              
              }
              setTimeout("checkForNewUsersOnline();", 20 * 1000);
         
        }
    });
    

}