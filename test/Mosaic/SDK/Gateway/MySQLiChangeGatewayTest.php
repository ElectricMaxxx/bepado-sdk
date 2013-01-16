<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Gateway;

/**
 * Test for the in mysqli gateway implementation.
 */
class MySQLiChangeGatewayTest extends ChangeGatewayTest
{
    /**
     * Factory method which creates our concrete SUT instance.
     *
     * @return \Mosaic\SDK\Gateway\ChangeGateway
     */
    protected function createChangeGateway()
    {
        $config = @parse_ini_file(__DIR__ . '/../../../../build.properties');
        $connection = new \Mosaic\SDK\MySQLi(
            $config['db.hostname'],
            $config['db.userid'],
            $config['db.password'],
            $config['db.name']
        );
        $connection->query('TRUNCATE TABLE mosaic_change;');
        $connection->query('TRUNCATE TABLE mosaic_product;');

        return new MySQLi($connection);
    }
}
