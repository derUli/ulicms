<?php
ini_set('memory_limit', '5120M');
set_time_limit(0);

class SqlUtils
{

    public function queryToStatements($sql_query)
    {
        $sql_query = $this->removeRemarks($sql_query);
        if (! str_contains(";", $sql_query)) {
            $sql_query .= ";";
        }
        $sql_query = $this->splitSqlFile($sql_query, ';');
        
        return $sql_query;
    }

    private function removeRemarks($sql)
    {
        $lines = explode("
", $sql);
        
        $sql = "";
        
        $linecount = count($lines);
        $output = "";
        
        for ($i = 0; $i < $linecount; $i ++) {
            if (($i != ($linecount - 1)) || (strlen($lines[$i]) > 0)) {
                if (isset($lines[$i][0]) && $lines[$i][0] != "#") {
                    $output .= $lines[$i] . "
";
                } else {
                    $output .= "
";
                }
                $lines[$i] = "";
            }
        }
        
        return $output;
    }

    private function splitSqlFile($sql, $delimiter)
    {
        $tokens = explode($delimiter, $sql);
        
        $sql = "";
        $output = array();
        
        $matches = array();
        
        $token_count = count($tokens);
        for ($i = 0; $i < $token_count; $i ++) {
            if (($i != ($token_count - 1)) || (strlen($tokens[$i] > 0))) {
                $total_quotes = preg_match_all("/'/", $tokens[$i], $matches);
                $escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$i], $matches);
                
                $unescaped_quotes = $total_quotes - $escaped_quotes;
                
                if (($unescaped_quotes % 2) == 0) {
                    $output[] = $tokens[$i];
                    $tokens[$i] = "";
                } else {
                    $temp = $tokens[$i] . $delimiter;
                    $tokens[$i] = "";
                    
                    $complete_stmt = false;
                    
                    for ($j = $i + 1; (! $complete_stmt && ($j < $token_count)); $j ++) {
                        $total_quotes = preg_match_all("/'/", $tokens[$j], $matches);
                        $escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$j], $matches);
                        
                        $unescaped_quotes = $total_quotes - $escaped_quotes;
                        
                        if (($unescaped_quotes % 2) == 1) {
                            $output[] = $temp . $tokens[$j];
                            
                            $tokens[$j] = "";
                            $temp = "";
                            
                            $complete_stmt = true;
                            $i = $j;
                        } else {
                            $temp .= $tokens[$j] . $delimiter;
                            $tokens[$j] = "";
                        }
                    }
                }
            }
        }
        
        return $output;
    }
}