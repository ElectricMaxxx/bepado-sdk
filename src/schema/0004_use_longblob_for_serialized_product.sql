ALTER TABLE `mosaic_change` MODIFY `c_product` LONGBLOB NULL;

--//@UNDO

ALTER TABLE `mosaic_change` MODIFY `c_product` BLOB NULL;
