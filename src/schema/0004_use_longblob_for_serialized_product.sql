ALTER TABLE `mosaic_change` MODIFY `c_product` LONGBLOB NOT NULL;

--//@UNDO

ALTER TABLE `mosaic_change` MODIFY `c_product` BLOB NOT NULL;
