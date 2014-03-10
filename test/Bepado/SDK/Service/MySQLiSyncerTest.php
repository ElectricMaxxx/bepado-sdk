<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 */

namespace Bepado\SDK\Service;

use Bepado\Common;
use Bepado\SDK;

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
        $connection = new \Bepado\SDK\MySQLi(
            $config['db.hostname'],
            $config['db.userid'],
            $config['db.password'],
            $config['db.name']
        );
        $connection->query('TRUNCATE TABLE bepado_change;');
        $connection->query('TRUNCATE TABLE bepado_product;');

        return $this->gateway = new SDK\Gateway\MySQLi($connection);
    }
}
