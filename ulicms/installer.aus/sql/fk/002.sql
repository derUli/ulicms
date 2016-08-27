ALTER TABLE `{prefix}videos`
  ADD CONSTRAINT fk_video_category 
  FOREIGN KEY (category_id) 
  REFERENCES `{prefix}categories`(id) 
  ON DELETE Set NULL;
