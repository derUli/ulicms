ALTER TABLE `{prefix}language`
  ADD CONSTRAINT fk_lastchangeby
  FOREIGN KEY (lastchangeby) 
  REFERENCES `{prefix}users`(id)
  ON DELETE Set NULL;