UPDATE {prefix}custom_fields 
SET    NAME = Concat((SELECT type 
                      FROM   {prefix}content 
                      WHERE  id = content_id), '_', NAME) 