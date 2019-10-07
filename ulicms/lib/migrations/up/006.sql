ALTER TABLE {prefix}banner CHANGE `category` `category_id` int(11) DEFAULT NULL;

ALTER TABLE `{prefix}banner`
  ADD CONSTRAINT `fk_category_id` FOREIGN KEY (`category_id`) REFERENCES `{prefix}categories` (`id`) ON DELETE SET NULL;