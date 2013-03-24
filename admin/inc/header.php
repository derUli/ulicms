<!doctype html>
<html>
<head>
<style type="text/css">
a,a:visited,a:active{
color:#000000;
text-decoration:none;
font-weight:bold;
font-size:1.0em;
}

a:hover{
color:red;
text-decoration:none;
font-weight:bold;
font-size:1.0em;
}

.menu a,.menu a:visited,.menu a:active{
color:#000000;
text-decoration:none;
font-weight:bold;
font-size:0.8em;
}

.menu a:hover{
color:red;
text-decoration:none;
font-weight:bold;
font-size:0.8em;
}

.cke a, .cke a:active, .cke a:hover{
color:black !important;
text-decoration:none !important;
font-weight:normal !important;
border:none !important;
font-size:1.0em;
}


strong{
color:#d25400;
}





.motd{
  overflow-x:no-scroll;  
  overflow-y:scroll;
  width:300px;
  height:160px;
  border:1px black solid;
  padding:10px 20px 10px 20px;
}



body{
background-color:#7FA5FF;
background-repeat:none;
background-attachment:fixed;
font-family:arial;
font-size:12pt;
color:#5b5c5b;

}

h1, h2, h3, h4, h5, h6{
color:#d25400;
}



#login{
background-color:#5aae85;
width:350px;
border:1px blue solid;
padding-left:20px;
padding-right:20px;
padding-bottom:20px;
}

/*
.menu{
background-color:#C4DBFF;
background-repeat:none;
background-attachment:fixed;
height:40px;
width:70%;
border:1px blue solid;
padding-left:20px;
padding-right:20px;
padding-top:5px;
font-family:arial;
font-size:9pt;
font-weight:bold;
}
*/


table{
empty-cells:show;
table-layout:auto;
}

iframe img{
border:0px;

}


img{
border:0px;
}

#pbody{
background-color:#ffffff;
color:#000000;
padding:10px 10px 10px 10px;
margin-top:20px;
border:1px blue solid;
width:98%;
}




input[type=button], input[type=submit]{
background-color:#aaa;
color:#fff;
width:200px;
border:6px solid #ddd; }

.startbutton,.startbutton:visited {
display:block;
color:#000000;
background-color:#fff;
width:450px;
padding:7px;
font-size:14px;
font-family:Verdana, Arial, sans-serif;
font-weight:bold;
text-decoration:none;
text-align:left;
margin:10px;
border:5px solid #000;
}
      
      
.startbutton:hover {
display:block;
color:white;
background-color:black;
}

.clear{
clear:both;
}

/** alle ULs */ 
ul.menu { 
  margin: 0; 
  padding: 0; 
  list-style: none; 
  z-index:992;
} 

ul.menu > li{
    position: relative;

}


/** ULs ab Level 2 */ 
ul.menu ul { 
   position: absolute;
    left: -999em; /** verstecken */ 

} 

/** ULs ab Level 3 */ 
ul.menu ul ul { 
  top: 50px; 
} 

/** Kind UL von gehoverten LIs */ 
.navbar_top li:hover > ul { 
  left: auto; 
margin-left:-37px;
} 

/** Kind UL von gehoverten LIs ab Level 2 */ 
.navbar_top li li:hover > ul { 
  left: 100%; 
} 

/** alle LIs */ 
.navbar_top li { 
  float: left; 
} 

/** alle LIs ab Level 2 */ 
.navbar_top li li { 
float: none; 
background-color:red;
list-style-type:none;
} 

/** alle As */ 
.navbar_top a { 
  display: block; 
  padding: .5em 1em; 
  background: #bbb; 
} 

/** As ab Level 2 */ 
.navbar_top li li a { 
  background: #ccc; 
} 

/** As ab Level 3 */ 
.navbar_top li li li a { 
  background: #ddd; 
  float: none;
  margin-left:-3px;
} 

/** As ab Level 4 */ 
.navbar_top li li li li a { 
  background: #eee; 
} 



</style>
<meta name="viewport" content="width=1280"/>

<script type="text/javascript" src="md5.js"></script>
<style type="text/css" media="screen">
/*.menu{
background-image:url(gfx/bg2.jpg);
}
*/

body{
background-image:url(gfx/bg.jpg);
}

</style>
<script type="text/javascript" src="jscolor/jscolor.js"></script>

<link rel="icon" href="gfx/favicon.ico" type="image/x-icon"> 
<link rel="shortcut icon" href="gfx/favicon.ico" type="image/x-icon">

<link rel="stylesheet" type="text/css" href="codemirror/lib/codemirror.css">
<script src="codemirror/lib/codemirror.js" type="text/javascript"></script>
<script src="codemirror/mode/php/php.js" type="text/javascript"></script>
<script src="codemirror/mode/xml/xml.js" type="text/javascript"></script>
<link rel="stylesheet" href="codemirror/mode/xml/xml.css" type="text/css">
<script src="codemirror/mode/javascript/javascript.js" type="text/javascript"></script>
<link rel="stylesheet" href="codemirror/mode/javascript/javascript.css" type="text/css">
<script src="codemirror/mode/clike/clike.js"></script>
<link rel="stylesheet" href="codemirror/mode/clike/clike.css" type="text/css">
<link rel="stylesheet" href="codemirror/mode/css/css.css" type="text/css">
<script src="codemirror/mode/css/css.js" type="text/javascript"></script>
<title>[<?php echo getconfig("homepage_title")?>] - UliCMS</title>
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
</head>
<body>