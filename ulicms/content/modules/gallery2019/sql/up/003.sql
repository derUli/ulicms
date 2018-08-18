ALTER TABLE `{prefix}gallery`
  ADD CONSTRAINT fk_gallery_createdby
  FOREIGN KEY (createdby) 
  REFERENCES `{prefix}users`(id)
  ON DELETE Set NULL;

  ALTER TABLE `{prefix}gallery`
  ADD CONSTRAINT fk_gallery_lastchangedby
  FOREIGN KEY (lastchangedby) 
  REFERENCES `{prefix}users`(id)
  ON DELETE Set NULL;
  
ALTER TABLE `{prefix}gallery_images`
  ADD CONSTRAINT fk_gallery_images_gallery_id
  FOREIGN KEY (gallery_id) 
  REFERENCES `{prefix}gallery`(id)
  ON DELETE CASCADE;