<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Gateway;

use Mosaic\SDK\Gateway;
use Mosaic\SDK\Struct;
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
    public function __construct(\Mosaic\SDK\MySQLi $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get next change
     *
     * The offset specified the revision to start from
     *
     * May remove all pending changes, which are prior to the last requested
     * revision.
     *
     * @param string $offset
     * @param int $limit
     * @return Struct\Change[]
     */
    public function getNextChanges($offset, $limit)
    {
        // @TODO:
        // * Fetch next changes
        // * Remove all changes, which are prior to the requested revision
        $result = $this->connection->query(
            'SELECT
                `c_source_id`,
                `c_operation`,
                `c_revision`
            FROM
                `mosaic_change`
            WHERE
                `c_revision` > ' . ((float) $offset) . '
            LIMIT
                ' . ((int) $limit)
        );

        $changes = array();
        while ($row = $result->fetch_assoc()) {
            $changes[] = new Struct\Change();
        }

        $this->connection->query(
            'DELETE FROM
                mosaic_change
            WHERE
                c_revision <= ' . ((float) $offset)
        );

        return $changes;
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
        $this->connection->query(
            'INSERT INTO
                mosaic_change
            VALUES (
                "' . $this->connection->real_escape_string($product->sourceId) . '",
                "insert",
                "' . $this->connection->real_escape_string($revision) . '",
                null
            );'
        );
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
        $this->connection->query(
            'INSERT INTO
                mosaic_change
            VALUES (
                "' . $this->connection->real_escape_string($product->sourceId) . '",
                "update",
                "' . $this->connection->real_escape_string($revision) . '",
                null
            );'
        );
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
        $this->connection->query(
            'INSERT INTO
                mosaic_change
            VALUES (
                "' . $this->connection->real_escape_string($product->sourceId) . '",
                "delete",
                "' . $this->connection->real_escape_string($revision) . '",
                null
            );'
        );
    }
}
