ALTER TABLE `{prefix}language`
  ADD CONSTRAINT fk_video
  FOREIGN KEY (video) 
  REFERENCES `{prefix}videos`(id)
  ON DELETE Set NULL;