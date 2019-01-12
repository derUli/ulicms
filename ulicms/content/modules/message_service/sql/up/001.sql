CREATE TABLE `{prefix}messages` 
  ( 
     `id`          INT NOT NULL auto_increment, 
     `message`     VARCHAR(1000) NOT NULL, 
     `sender_id`   INT NOT NULL, 
     `receiver_id` INT NOT NULL, 
     PRIMARY KEY (`id`), 
     CONSTRAINT fk_message_sender FOREIGN KEY (sender_id) REFERENCES {prefix}users (id) 
     ON DELETE CASCADE, 
     CONSTRAINT fk_message_receiver FOREIGN KEY (receiver_id) REFERENCES {prefix}users ( 
     id) ON DELETE CASCADE 
  ) 
engine = innodb 
charset=utf8mb4 
COLLATE utf8mb4_general_ci; 