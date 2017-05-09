ALTER TABLE `{prefix}forms`
  ADD CONSTRAINT fk_target_page_id
  FOREIGN KEY (target_page_id)
  REFERENCES `{prefix}content`(id)
  ON DELETE Set NULL;

