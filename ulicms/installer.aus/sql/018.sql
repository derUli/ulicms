CREATE TABLE IF NOT EXISTS `{prefix}categories` (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    `description` TEXT NULL DEFAULT ''
    ) ENGINE=InnoDB DEFAULT charset=utf8mb4 AUTO_INCREMENT=1;
