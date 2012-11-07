-- Table: Data (d)
CREATE TABLE IF NOT EXISTS `mosaic_data` (
  `d_key` VARCHAR(32) NOT NULL,
  `d_value` VARCHAR(256) NOT NULL,
  `changed` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`d_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
