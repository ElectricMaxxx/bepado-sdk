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
     * Get next changes
     *
     * @param int $limit
     * @return Struct\Changes[]
     */
    public function getNextChanges($limit)
    {
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
                changes
            VALUES (
                "' . $this->connection->real_escape_string($product->sourceId) . '",
                "' . $this->connection->real_escape_string(md5(serialize($product))) . '",
                ' . time() . ',
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
        throw new \RuntimeException('@TODO: Implement');
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
        throw new \RuntimeException('@TODO: Implement');
    }
}
