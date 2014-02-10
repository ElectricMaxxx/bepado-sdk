<?php

namespace Bepado\SDK;

use Behat\Behat\Context\ClosuredContextInterface;
use Behat\Behat\Context\TranslatedContextInterface;
use Behat\Behat\Context\BehatContext;
use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Bepado\SDK\Struct\Product;
use Bepado\SDK\Struct\Change;
use Bepado\Common\Rpc;
use Bepado\Common\Struct;

use \PHPUnit_Framework_Assert as Assertion;

require_once __DIR__ . '/SDKContext.php';

/**
 * Features context.
 */
class ShippingCostsContext extends SDKContext
{
    protected $shopRevision = null;

    /**
     * @When /^The updater requests the shipping costs revision$/
     */
    public function theUpdaterRequestsTheShippingCostsRevision()
    {
        $this->shopRevision = $this->makeRpcCall(
            new Struct\RpcCall(
                array(
                    'service' => 'shippingCosts',
                    'command' => 'lastRevision',
                    'arguments' => array(),
                )
            )
        );
    }

    /**
     * @Then /^The shipping costs revision is "([^"]*)"$/
     */
    public function theShippingCostsRevisionIs($revision)
    {
        Assertion::assertEquals($revision, $this->shopRevision);
    }
}
