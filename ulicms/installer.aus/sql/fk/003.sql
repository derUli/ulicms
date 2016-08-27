ALTER TABLE `{prefix}videos`
  ADD CONSTRAINT fk_category 
  FOREIGN KEY (category) 
  REFERENCES `{prefix}categories`(id) 
  ON DELETE Set NULL;