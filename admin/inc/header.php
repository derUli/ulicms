<!doctype html>
<html>
<head>
<style type="text/css">
a,a:visited,a:active{
color:blue;
text-decoration:none;
}

a:hover{
color:red;
text-decoration:none;
}


.motd{
  overflow-x:no-scroll;  
  overflow-y:scroll;
  width:300px;
  height:160px;
  border:1px black solid;
  padding:10px 20px 10px 20px;
}

*[data-tooltip]
        {
        cursor:help;
        }
        
        
 /* Styles for elements having a data-tooltip attribute - using the star selector is processor intensive
     so you may wish to change this to list a known, limited set of tags instead */
  *[data-tooltip]
        {
        /* Relativly position the tooltip to enable us to position:absolute 
           the generated content */
        position:relative;        
        /* Links inherit the !important cursor rule from above */
        cursor:help;
        /* Moz requires the text-decoration here (as it won't allow the use of
          text-decoration:none on generated content) which is why I use the bottom 
          border to display a more accessible underline */
        text-decoration:none;   
        border-bottom:1px dotted #aaa; 
        /* Remove the styles for IE7 and below - could be passed using conditional comments */
        *text-decoration:inherit;   
        *border-bottom-width:inherit;
        *border-bottom-style:inherit;        
        *cursor:inherit;
        *position:inherit;            
        }
  /* Default :before & :after values */     
  *[data-tooltip]:after,
  *[data-tooltip]:before
        {
        content:"";
        /* Don't show tooltip by default */
        opacity:0;  
        /* Set a high z-index */
        z-index:999;
        
        /* Animations won't (yet) work on pseudo elements - shame really as this should fade the tooltip in 
           after one second - but I'll leave the rules for posterity */
        -moz-transition-property: opacity;
        -moz-transition-duration: 2s;
        -moz-transition-delay: 1s;
        
        -webkit-transition-property: opacity;
        -webkit-transition-duration: 2s;
        -webkit-transition-delay: 1s;
        
        -o-transition-property: opacity;
        -o-transition-duration: 2s;
        -o-transition-delay: 1s;
        
        transition-property: opacity;
        transition-duration: 2s;
        transition-delay: 1s;  
        
        /* -moz won't understand the text-decoration here but inherits the parent value of "none" successfully */
        text-decoration:none !important;
        outline:none;
        }
  /* Tooltip arrow - shown on hover or focus */
  *[data-tooltip]:hover:before,
  *[data-tooltip]:focus:before
        {
        /* Slightly opaque arrow */
        opacity:0.94;
        outline:none;
        content:"";  
        display:block;        
        position:absolute;
        top:20px;
        left:50%;
        margin:0 0 0 -5px;
        width:0;
        height:0;
        line-height:0px; 
        font-size:0px;       
        /* This sets the tooptip pointer color */
        border-bottom:5px solid #33acfc;
        border-left:5px solid transparent;
        border-right:5px solid transparent;        
        border-top:transparent;
        /* Border gradient */        
        -webkit-border-image:-webkit-gradient(linear, left top, left bottom, from(#33ccff), to(#33acfc));                        
        }
  /* Tooltip body - shown on hover or focus */
  *[data-tooltip]:hover:after,
  *[data-tooltip]:focus:after
        {
        /* Slightly opaque tooltip */
        opacity:0.94;
        /* Set display to block (or inline-block) */
        display:block;
        /* Use the data-tooltip attribute to set the content*/
        content:attr(data-tooltip);
        /* Position the tooltip body under the arrow and in the middle of the text */
        position:absolute;
        top:25px;
        left:50%;
        margin:0 0 0 -50px;
        /* Set the width */
        width:290px;
        /* Pad */
        padding:5px;
        /* Style the tooltip */
        line-height:18px;
        /* min-height */
        min-height:18px; 
        /* Set font styles */  
        color:#fcfcfc;
        font-size:16px;        
        font-weight:normal;
        font-family:helvetica neue, calibri, verdana, arial, sans-serif;
        /* Fallback background color */
        background:#3198dd; 
        text-align:center;        
        outline:none;        
        /* Moz doesn't recognise the following... */
        text-decoration:none !important;                  
        /* Background gradient */        
        background:-webkit-gradient(linear, left top, left bottom, from(#33acfc), to(#3198dd));
        background:-moz-linear-gradient(top,#33acfc,#3198dd);         
        /* Round the corners */
        -moz-border-radius:10px;
        -webkit-border-radius:10px;
        border-radius:10px;        
        /* Add a drop shadow */
        -moz-box-shadow:2px 2px 4px #ccc;
        -webkit-box-shadow:2px 2px 4px #ccc;
        box-shadow:2px 2px 4px #ccc;        
        /* Add a Text shadow */
        text-shadow:#2187c8 0 1px 0px; 
        }  


body{
background-color:#7FA5FF;
background-image:url(gfx/bg.jpg);
background-repeat:none;
background-attachment:fixed;
font-family:arial;
font-size:12pt;

}

h2{
color:#6A26FF;
}

h3{
color:#2D4DFF;
}

#login{
background-color:#2DB2FF;
width:350px;
border:1px blue solid;
padding-left:20px;
padding-right:20px;
padding-bottom:20px;
}

#menu{
background-color:#C4DBFF;
background-image:url(gfx/bg2.jpg);
background-repeat:none;
background-attachment:fixed;
height:40px;
width:800px;
border:1px blue solid;
padding-left:20px;
padding-right:20px;
padding-top:5px;
font-family:arial;
font-size:9pt;
font-weight:bold;
}


table{
empty-cells:show;
table-layout:auto;
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


</style>

<script type="text/javascript" src="md5.js"></script>
<link rel="icon" href="gfx/favicon.ico" type="image/x-icon"> 
<link rel="shortcut icon" href="gfx/favicon.ico" type="image/x-icon">

<link rel="stylesheet" type="text/css" href="codemirror/lib/codemirror.css">
<script src="codemirror/lib/codemirror.js" type="text/javascript"></script>
<script src="codemirror/mode/php/php.js" type="text/javascript"></script>
<link rel="stylesheet" href="codemirror/mode/php/php.css" type="text/css">
<script src="codemirror/mode/xml/xml.js" type="text/javascript"></script>
<link rel="stylesheet" href="codemirror/mode/xml/xml.css" type="text/css">
<script src="codemirror/mode/javascript/javascript.js" type="text/javascript"></script>
<link rel="stylesheet" href="codemirror/mode/xml/javascript.css" type="text/css">
<script src="codemirror/mode/clike/clike.js"></script>
<link rel="stylesheet" href="codemirror/mode/clike/clike.css" type="text/css">
<link rel="stylesheet" href="codemirror/mode/css/css.css" type="text/css">
<script src="codemirror/mode/css/css.js" type="text/javascript"></script>
<title>[<?php echo getconfig("homepage_title")?>] - UliCMS</title>
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
</head>
<body>