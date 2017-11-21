ALTER TABLE `{prefix}content`
  ADD CONSTRAINT fk_module
  FOREIGN KEY (module)
  REFERENCES `{prefix}modules`(name)
  ON DELETE NO ACTION
