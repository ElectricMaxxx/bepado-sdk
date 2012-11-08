<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Service;

use Mosaic\Common;
use Mosaic\SDK;

require_once __DIR__ . '/SyncerTest.php';

class MySQLiSyncerTest extends SyncerTest
{
    protected $gateway;

    /**
     * Get used gateway for test
     *
     * @return SDK\Gateway
     */
    protected function getGateway()
    {
        if ($this->gateway) {
            return $this->gateway;
        }

        $config = @parse_ini_file(__DIR__ . '/../../../../build.properties');
        $connection = new \Mosaic\SDK\MySQLi(
            $config['db.hostname'],
            $config['db.userid'],
            $config['db.password'],
            $config['db.name']
        );
        $connection->query('TRUNCATE TABLE mosaic_change;');
        $connection->query('TRUNCATE TABLE mosaic_product;');

        return $this->gateway = new SDK\Gateway\MySQLi($connection);
    }
}
