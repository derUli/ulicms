<?php

if(!isset($_SESSION["sortDirection"])){
     $_SESSION["sortDirection"] = "asc";
    }

if(isset($_REQUEST["sort_direction"])){
     if($_SESSION["sortDirection"] == "asc")
         $_SESSION["sortDirection"] = "desc";
     else
         $_SESSION["sortDirection"] = "asc";
    }
