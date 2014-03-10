<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 */

namespace Bepado\SDK\Gateway;

/**
 * Test for the in mysqli gateway implementation.
 */
class MySQLiChangeGatewayTest extends ChangeGatewayTest
{
    /**
     * Factory method which creates our concrete SUT instance.
     *
     * @return \Bepado\SDK\Gateway\ChangeGateway
     */
    protected function createChangeGateway()
    {
        $config = @parse_ini_file(__DIR__ . '/../../../../build.properties');
        $connection = new \Bepado\SDK\MySQLi(
            $config['db.hostname'],
            $config['db.userid'],
            $config['db.password'],
            $config['db.name']
        );
        $connection->query('TRUNCATE TABLE bepado_change;');
        $connection->query('TRUNCATE TABLE bepado_product;');

        return new MySQLi($connection);
    }
}
