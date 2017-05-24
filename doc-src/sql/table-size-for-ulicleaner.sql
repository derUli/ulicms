SELECT 
  TABLE_NAME, table_rows, data_length, index_length,  
  round(((data_length + index_length) / 1024 / 1024),2) as size_in_mb
FROM information_schema.TABLES 
WHERE TABLE_NAME in ('deruli2015_log', 'deruli2015_history', 'deruli2015_mails') and TABLE_TYPE='BASE TABLE' and data_length + index_length > 0
ORDER BY data_length DESC;
