<?php

namespace Bepado\SDK;

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Bepado\SDK\Struct\ShopConfiguration;
use Bepado\Common\Struct;

use \PHPUnit_Framework_Assert as Assertion;

require_once __DIR__ . '/SDKContext.php';

/**
 * Features context.
 */
class PushShopConfigContext extends SDKContext
{
    protected $configs = array();

    /**
     * @Given /^The shop imports configuration from (\d+) other shops?$/
     */
    public function theShopImportsConfigurationFromOtherShop($shopCount)
    {
        for ($i = 0; $i < $shopCount; ++$i) {
            $this->configs['shop-' . $i] = new ShopConfiguration(
                array(
                    'name' => 'Shop ' . $i,
                    'serviceEndpoint' => 'http://example.com/shop/' . $i,
                )
            );
        }
    }

    /**
     * @When /^Bepado synchronizes data with the shop$/
     */
    public function bepadoSynchronizesDataWithTheShop()
    {
        $this->makeRpcCall(
            new Struct\RpcCall(array(
                'service' => 'configuration',
                'command' => 'update',
                'arguments' => array(
                    $this->configs
                )
            ))
        );
    }

    /**
     * @Then /^The shop receives (\d+) shop configuration updates?$/
     */
    public function theShopReceivesShopConfigurationUpdate($configCount)
    {
        Assertion::assertEquals(
            $configCount,
            count($this->configs)
        );
    }

}
