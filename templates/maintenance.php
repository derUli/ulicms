<!doctype html>
<html>
<head>
<title><?php homepage_title()?> | <?php title()?></title>
<?php base_metas()?>
<style type="text/css">
body{
background-color:rgb(255,255,255);
text-center;
margin:0px;
}

html{
overflow-x:hidden;
overflow-y:scroll;
}

.header span{
font-family:arial;
font-size:9pt;
}

.header{
background-color:rgb(50, 109, 248);
color:rgb(255, 255, 255);
float:left;
width:100%;
padding-left:30px;
padding-right:30px;
padding-bottom:20px;
margin:0px;
height:100px;
}

.header .logo{
float:left;
margin-right:30px;
text-align:center;
font-family:'Times New Roman',Times,serif;
}

.header .navbar_top{
float:left;
margin-left:5%;
margin-right:1%;
margin-top:30px;
font-family:verdana;
font-size:9pt;
}

.header .navbar_top ul{
display:inline;
list-style-type:none;
display:inline;
}

.menu_bottom ul{
display:inline;
list-style-type:none;
}


a{
color:rgb(0, 128, 255);
text-decoration:none;
}

a:visited{
color:rgb(0, 106, 157);
text-decoration:none;
}

a:hover{
color:rgb(255, 128, 0);
text-decoration:none;
}

.header .navbar_top ul li{
float:left;
margin-right:20px;
}

.navbar_down ul li{
float:left;
margin-right:20px;
}

.header .navbar_top ul li a, .header .navbar_top ul li a:visited{
color:white;
text-decoration:none;
}

.header .navbar_top ul li a:hover{
font-style:italic;
}


.clear{
clear:both;
}

.content{
padding:20px;
float:left;
width:60%;
font-family:arial;
font-size:11pt;
}


.news{
margin-top:20px;
margin-bottom:20px;
float:right;
width:20%;
font-family:arial;
font-size:9pt;
}

h1,h2,h3,h4,h5,h6{
font-family:Balloon
}

.navbar_down{
margin:auto;
margin-left:10px;
margin-right:10px
}

div.navbar_top, div.navbar_down{
color:rgb(255, 128, 0);
}


.navbar_down ul li a, .navbar_down ul li a:visited{
color:rgb(0, 128, 192);
font-weight:normal;
text-decoration:none;
}

.navbar_down ul li a:hover{
color:rgb(0, 128, 192);
font-weight:bold;
text-decoration:none;
}



.content h1,.content h2,.content h3,.content h4,.content h5,.content h6{
color:rgb(52, 106, 250);
font-family:arial;
}

.copyright{
text-align:center;
margin:20px;
color:rgb(52, 106, 250);
}

</style>
</head>
<body>
<div class="header">
<div class="logo">
<h1><?php homepage_title()?></h1>
<span><?php motto()?></span>
</div>
<div class="navbar_top">
</div>
</div>
<div class="clear"></div>
<div class="container">
<div class="content">
<h2>Wartungsmodus</h2>
<hr>

<p>Die Seite befindet sich momentan im Wartungsmodus.<br>
Bitte versuchen Sie es später wieder.</p> 

<br><br>
<br>
</div>
</div>
<div class="news">
</div>
<div style="clear:both;">
</div>

<div class="navbar_down">
</div>
<div class="clear">
</div>


<div class="copyright">
<p>&copy; <?php year()?> by  <?php homepage_owner()?></p>
</div>
</body>
</html>
