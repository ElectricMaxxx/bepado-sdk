<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Gateway;

use Mosaic\SDK\Gateway;
use Mosaic\SDK\Struct;
use Mosaic\SDK\Struct\Product;

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
     * Struct classes used for operations
     *
     * @var array
     */
    protected $operationStruct = array(
        'insert' => '\\Mosaic\\SDK\\Struct\\Change\\Insert',
        'update' => '\\Mosaic\\SDK\\Struct\\Change\\Update',
        'delete' => '\\Mosaic\\SDK\\Struct\\Change\\Delete',
    );

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
        $offset = $offset ?: 0;
        // Float type cast does NOT work here, since the inaccuracy of floating
        // point representations otherwise omit changes. Yes, this actually
        // really happens.
        if (!preg_match('(^[\\d\\.]+$)', $offset)) {
            throw new \InvalidArgumentException("Offset revision must be a numeric string.");
        }

        $result = $this->connection->query(
            'SELECT
                `c_source_id`,
                `c_operation`,
                `c_revision`
            FROM
                `mosaic_change`
            WHERE
                `c_revision` > ' . $offset . '
            LIMIT
                ' . ((int) $limit)
        );

        $changes = array();
        while ($row = $result->fetch_assoc()) {
            $class = $this->operationStruct[$row['c_operation']];
            $changes[] = new $class(
                array(
                    'sourceId' => $row['c_source_id'],
                    'revision' => $row['c_revision'],
                )
            );
        }

        $this->connection->query(
            'DELETE FROM
                mosaic_change
            WHERE
                c_revision <= ' . $offset
        );

        return $changes;
    }

    /**
     * Record product insert
     *
     * @param string $id
     * @param string $hash
     * @param string $revision
     * @return void
     */
    public function recordInsert($id, $hash, $revision)
    {
        $this->connection->query(
            'INSERT INTO
                mosaic_change
            VALUES (
                "' . $this->connection->real_escape_string($id) . '",
                "insert",
                "' . $this->connection->real_escape_string($revision) . '",
                null
            );'
        );

        $this->connection->query(
            'INSERT INTO
                mosaic_product
            VALUES (
                "' . $this->connection->real_escape_string($id) . '",
                "' . $this->connection->real_escape_string($hash) . '",
                null
            );'
        );
    }

    /**
     * Record product update
     *
     * @param string $id
     * @param string $hash
     * @param string $revision
     * @return void
     */
    public function recordUpdate($id, $hash, $revision)
    {
        $this->connection->query(
            'INSERT INTO
                mosaic_change
            VALUES (
                "' . $this->connection->real_escape_string($id) . '",
                "update",
                "' . $this->connection->real_escape_string($revision) . '",
                null
            );'
        );

        $this->connection->query(
            'UPDATE
                mosaic_product
            SET
                p_hash = "' . $this->connection->real_escape_string($hash) . '"
            WHERE
                p_source_id = "' . $this->connection->real_escape_string($id) . '"
            ;'
        );
    }

    /**
     * Record product delete
     *
     * @param string $id
     * @param string $hash
     * @param string $revision
     * @return void
     */
    public function recordDelete($id, $revision)
    {
        $this->connection->query(
            'INSERT INTO
                mosaic_change
            VALUES (
                "' . $this->connection->real_escape_string($id) . '",
                "delete",
                "' . $this->connection->real_escape_string($revision) . '",
                null
            );'
        );

        $this->connection->query(
            'DELETE FROM
                mosaic_product
            WHERE
                p_source_id = "' . $this->connection->real_escape_string($id) . '"
            ;'
        );
    }

    /**
     * Check if product has changed
     *
     * Return true, if product chenged since last check.
     *
     * @param string $id
     * @param string $hash
     * @return boolean
     */
    public function hasChanged($id, $hash)
    {
        $result = $this->connection->query(
            'SELECT
                `p_hash`
            FROM
                `mosaic_product`
            WHERE
                p_source_id = "' . $this->connection->real_escape_string($id) . '"'
        );

        $row = $result->fetch_assoc();
        return $row['p_hash'] !== $hash;
    }

    /**
     * Get IDs of all recorded products
     *
     * @return string[]
     */
    public function getAllProductIDs()
    {
        $result = $this->connection->query(
            'SELECT
                `p_source_id`
            FROM
                `mosaic_product`'
        );

        return array_map(
            function ($row) {
                return $row['p_source_id'];
            },
            $result->fetch_all(\MYSQLI_ASSOC)
        );
    }
}
