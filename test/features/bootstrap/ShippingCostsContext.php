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
use Bepado\SDK\ShippingCosts\Rule;
use Bepado\SDK\ShippingCosts\Rules;
use Bepado\SDK\Rpc;
use Bepado\SDK\Struct;

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
     * @When /^Shipping costs are pushed to the SDK for shop "([^"]*)" with revision "([^"]*)"$/
     */
    public function shippingCostsArePushedToTheSdkForShopWithRevision($shop, $revision)
    {
        $rule = new Rule\FixedPrice(array('price' => 10));

        $this->shopRevision = $this->makeRpcCall(
            new Struct\RpcCall(
                array(
                    'service' => 'shippingCosts',
                    'command' => 'replicate',
                    'arguments' => array(
                        'changes' => array(
                            array(
                                'to_shop_id' => $shop,
                                'from_shop_id' => $shop,
                                'revision' => $revision,
                                'shippingCosts' => new Rules(array('rules' => $rule)),
                            ),
                        ),
                    ),
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
