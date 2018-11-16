CREATE TABLE `{prefix}comments` 
  ( 
     `id`           INT NOT NULL auto_increment, 
     `content_id`   INT NOT NULL, 
     `author_name`  VARCHAR(255) NOT NULL, 
     `author_email` VARCHAR(255) NULL,
     `author_url`   VARCHAR(255) NULL,
     `date`         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
     `content`      TEXT NOT NULL, 
     `status`       VARCHAR(50) NOT NULL DEFAULT 'pending', 
     `ip`           VARCHAR(255) NULL DEFAULT NULL,
     `useragent`    TEXT NULL DEFAULT NULL,
     FOREIGN KEY (`content_id`) REFERENCES {prefix}content(id) ON DELETE CASCADE, 
     PRIMARY KEY (`id`) 
  )