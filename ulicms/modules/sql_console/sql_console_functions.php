<?php
ini_set('memory_limit', '5120M');
set_time_limit (0);
/**
 * sql_parse.php
 *                                                -------------------
 *                       begin                : Thu May 31, 2001
 *                       copyright            : (C) 2001 The phpBB Group
 *                       email                : support@phpbb.com
 * 
 *                       $Id: sql_parse.php,v 1.8 2002/03/18 23:53:12 psotfx Exp $
 */

/**
 * This program is free software; you can redistribute it and/or modify
 *                     it under the terms of the GNU General Public License as published by
 *                     the Free Software Foundation; either version 2 of the License, or
 *                     (at your option) any later version.
 */

/**
 * These functions are mainly for use in the db_utilities under the admin
 *                     however in order to make these functions available elsewhere, specifically
 *                     in the installation phase of phpBB I have seperated out a couple of
 *                     functions into this file.  JLH
 * 
 * \**************************************************************************
 */


// remove_comments will strip the sql comment lines out of an uploaded sql file
// specifically for mssql and postgres type files in the install....
function remove_comments(& $output)
{
     $lines = explode("
", $output);
     $output = "";
    
     // try to keep mem. use down
    $linecount = count($lines);
    
     $in_comment = false;
     for($i = 0; $i < $linecount; $i++)
    {
         if(preg_match("/^\/\*/", preg_quote($lines[$i])))
            {
             $in_comment = true;
             }
        
         if(!$in_comment)
        {
             $output .= $lines[$i] . "
";
             }
        
         if(preg_match("/\*\/$/", preg_quote($lines[$i])))
            {
             $in_comment = false;
             }
         }
    
     unset($lines);
     return $output;
     }


// remove_remarks will strip the sql comment lines out of an uploaded sql file
function remove_remarks($sql)
{
     $lines = explode("
", $sql);
    
     // try to keep mem. use down
    $sql = "";
    
     $linecount = count($lines);
     $output = "";
    
     for ($i = 0; $i < $linecount; $i++)
    {
         if (($i != ($linecount - 1)) || (strlen($lines[$i]) > 0))
            {
             if (isset($lines[$i][0]) && $lines[$i][0] != "#")
                {
                 $output .= $lines[$i] . "
";
                 }
            else
                {
                 $output .= "
";
                 }
             // Trading a bit of speed for lower mem. use here.
            $lines[$i] = "";
             }
         }
    
     return $output;
    
     }


// split_sql_file will split an uploaded sql file into single sql statements.
// Note: expects trim() to have already been run on $sql.
function split_sql_file($sql, $delimiter)
{
     // Split up our string into "possible" SQL statements.
    $tokens = explode($delimiter, $sql);
    
     // try to save mem.
    $sql = "";
     $output = array();
    
     // we don't actually care about the matches preg gives us.
    $matches = array();
    
     // this is faster than calling count($oktens) every time thru the loop.
    $token_count = count($tokens);
     for ($i = 0; $i < $token_count; $i++)
    {
         // Don't wanna add an empty string as the last thing in the array.
        if (($i != ($token_count - 1)) || (strlen($tokens[$i] > 0)))
            {
             // This is the total number of single quotes in the token.
            $total_quotes = preg_match_all("/'/", $tokens[$i], $matches);
             // Counts single quotes that are preceded by an odd number of backslashes,
            // which means they're escaped quotes.
            $escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$i], $matches);
            
             $unescaped_quotes = $total_quotes - $escaped_quotes;
            
             // If the number of unescaped quotes is even, then the delimiter did NOT occur inside a string literal.
            if (($unescaped_quotes % 2) == 0)
                {
                 // It's a complete sql statement.
                $output[] = $tokens[$i];
                 // save memory.
                $tokens[$i] = "";
                 }
            else
                {
                 // incomplete sql statement. keep adding tokens until we have a complete one.
                // $temp will hold what we have so far.
                $temp = $tokens[$i] . $delimiter;
                 // save memory..
                $tokens[$i] = "";
                
                 // Do we have a complete statement yet?
                $complete_stmt = false;
                
                 for ($j = $i + 1; (!$complete_stmt && ($j < $token_count)); $j++)
                {
                     // This is the total number of single quotes in the token.
                    $total_quotes = preg_match_all("/'/", $tokens[$j], $matches);
                     // Counts single quotes that are preceded by an odd number of backslashes,
                    // which means they're escaped quotes.
                    $escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$j], $matches);
                    
                     $unescaped_quotes = $total_quotes - $escaped_quotes;
                    
                     if (($unescaped_quotes % 2) == 1)
                        {
                         // odd number of unescaped quotes. In combination with the previous incomplete
                        // statement(s), we now have a complete statement. (2 odds always make an even)
                        $output[] = $temp . $tokens[$j];
                        
                         // save memory.
                        $tokens[$j] = "";
                         $temp = "";
                        
                         // exit the loop.
                        $complete_stmt = true;
                         // make sure the outer loop continues at the right point.
                        $i = $j;
                         }
                    else
                        {
                         // even number of unescaped quotes. We still don't have a complete statement.
                        // (1 odd and 1 even always make an odd)
                        $temp .= $tokens[$j] . $delimiter;
                         // save memory.
                        $tokens[$j] = "";
                         }
                    
                     } // for..
                 } // else
             }
         }
    
     return $output;
     }

function display_error(){
     echo "<p class=\"sql_error\">" . db_error() . "</p>";
     }





function sqlQueryFromString($sql_query){
    
     $sql_query = remove_remarks($sql_query);
     $sql_query = split_sql_file($sql_query, ';');
    
     foreach($sql_query as $sql){
         $query = false;
         $query = db_query($sql);
        
         if(!$query){
             display_error();
             return false;
             }
        
         echo "<p>" . db_affected_rows() . " rows affected" . "</p>";
        
         if($query !== false and $query !== true){
             $fields_num = db_num_fields($query);
             if($fields_num){
                 echo "<div style=\"overflow:auto; width: 98%; height:400px; margin:auto;\">";
                 echo "<table border='1' width='100%'><tr>";
                 // printing table headers
                for($i = 0; $i < $fields_num; $i++)
                {
                     $field = db_fetch_field($query);
                     echo "<td style=\"font-weight:bold;\">{$field->name}</td>";
                     }
                 echo "</tr>
";
                 // printing table rows
                while($row = db_fetch_row($query))
                {
                     echo "<tr>";
                    
                     // $row is array... foreach( .. ) puts every element
                    // of $row to $cell variable
                    foreach($row as $cell){
                         $txt = htmlspecialchars($cell, ENT_COMPAT, "UTF-8");
                         $txt = nl2br($txt);
                         echo "<td>$txt</td>";
                        
                         }
                    
                     echo "</tr>
";
                     }
                
                 echo "</table></div>
<br/>";
                
                 }
            
             }
        
         }
    
     return true;
    
     }



?>