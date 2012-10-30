<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Gateway;

use Mosaic\SDK\Gateway;
use Mosaic\SdkApi\Struct\Product;

/**
 * Default MySQLi implementation of the storage gateway
 *
 * @version $Revision$
 */
class MySQLi extends Gateway
{
    /**
     * MySQLi connection
     *
     * @var \Mosaic\SDK\MySQLi
     */
    protected $connection;

    /**
     * Construct from MySQL connection
     *
     * @param \Mosaic\SDK\MySQLi $connection
     */
    public function __construct( \Mosaic\SDK\MySQLi $connection )
    {
        $this->connection = $connection;
    }

    /**
     * Get next change
     *
     * @param int $limit
     * @return Struct\Changes[]
     */
    public function getNextChanges($limit)
    {
        // @TODO:
        // * Fetch next changes
        // * Update latest revision
        // * Remove all changes, which are processed
        throw new \RuntimeException('@TODO: Implement');
    }

    /**
     * Record product insert
     *
     * @param Struct\Product $product
     * @param string $revision
     * @return void
     */
    public function recordInsert(Product $product, $revision)
    {
        $this->connection->query('
            INSERT INTO
                mosaic_change
            VALUES (
                "' . $this->connection->real_escape_string($product->sourceId) . '",
                "insert",
                "' . $this->connection->real_escape_string($revision) . '",
                null
            );
        ');
    }

    /**
     * Record product update
     *
     * @param Struct\Product $product
     * @param string $revision
     * @return void
     */
    public function recordUpdate(Product $product, $revision)
    {
        $this->connection->query('
            INSERT INTO
                mosaic_change
            VALUES (
                "' . $this->connection->real_escape_string($product->sourceId) . '",
                "update",
                "' . $this->connection->real_escape_string($revision) . '",
                null
            );
        ');
    }

    /**
     * Record product delete
     *
     * @param Struct\Product $product
     * @param string $revision
     * @return void
     */
    public function recordDelete(Product $product, $revision)
    {
        $this->connection->query('
            INSERT INTO
                mosaic_change
            VALUES (
                "' . $this->connection->real_escape_string($product->sourceId) . '",
                "delete",
                "' . $this->connection->real_escape_string($revision) . '",
                null
            );
        ');
    }
}
