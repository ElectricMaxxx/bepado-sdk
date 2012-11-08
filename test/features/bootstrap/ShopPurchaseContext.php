<?php

namespace Mosaic\SDK;

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Mosaic\SDK\Struct;
use Mosaic\SDK\Controller;
use Mosaic\Common\RPC;

use \PHPUnit_Framework_Assert as Assertion;

require_once __DIR__ . '/SDKContext.php';

/**
 * Features context.
 */
class ShopPurchaseContext extends SDKContext
{
    /**
     * Currently processed order
     *
     * @var Struct\Order
     */
    protected $order;

    /**
     * Result of last processing
     *
     * @var mixed
     */
    protected $result;

    /**
     * @Given /^The product is listed as available$/
     */
    public function theProductIsListedAsAvailable()
    {
        // Nothing?
    }

    /**
     * @Given /^A customer adds a product from remote shop (\d+) to basket$/
     */
    public function aCustomerAddsAProductFromARemoteShopToBasket($remoteShop)
    {
        $this->order = new Struct\Order(
            array(
                'products' => array(
                    new Struct\OrderItem(
                        array(
                            'count' => 1,
                            'product' => new Struct\Product(
                                array(
                                    'shopId' => 'shop-' . $remoteShop,
                                    'sourceId' => '23',
                                    'price' => 42.23,
                                    'currency' => 'EUR',
                                    'availability' => 5,
                                )
                            ),
                        )
                    )
                ),
            )
        );
    }

    /**
     * @When /^The Customer checks out$/
     */
    public function theCustomerChecksOut()
    {
        $this->result = $this->sdk->checkout(
            $this->sdk->reserveProducts($this->order)
        );
    }

    /**
     * @Then /^The customer will receive the products?$/
     */
    public function theCustomerWillReceiveTheProducts()
    {
        Assertion::assertTrue($this->result);
    }

    /**
     * @Given /^The product is not available in remote shop$/
     */
    public function theProductIsNotAvailableInRemoteShop()
    {
        throw new PendingException(
           'Reconfigure target shops ProductFromShop'
        );
    }

    /**
     * @When /^The Customer views the order overview$/
     */
    public function theCustomerViewsTheOrderOverview()
    {
        $this->result = $this->sdk->checkProducts($this->order);
    }

    /**
     * @Then /^The customer is informed about the unavailability$/
     */
    public function theCustomerIsInformedAboutTheUnavailability()
    {
        throw new PendingException(
           'Assert $this->result is a sane message'
        );
    }

    /**
     * @Given /^The product price has changed in the remote shop$/
     */
    public function theProductPriceHasChangedInTheRemoteShop()
    {
        throw new PendingException(
           'Reconfigure target shops ProductFromShop'
        );
    }

    /**
     * @Then /^The customer is informed about the changed price$/
     */
    public function theCustomerIsInformedAboutTheChangedPrice()
    {
        throw new PendingException(
           'Assert $this->result is a sane message'
        );
    }

    /**
     * @Then /^The product is reserved in the remote shop$/
     */
    public function theProductIsReservedInTheRemoteShop()
    {
        throw new PendingException(
           'Assert $this->result is a reservationId'
        );
    }

    /**
     * @Given /^The buy process fails$/
     */
    public function theBuyProcessFails()
    {
        throw new PendingException(
           'Make buy process fail somehow'
        );
    }

    /**
     * @Then /^The customer is informed about this\.$/
     */
    public function theCustomerIsInformedAboutThis()
    {
        throw new PendingException(
           'Assert $this->result is a sane message'
        );
    }

    /**
     * @Then /^The (local|remote) shop logs the transaction with Mosaic$/
     */
    public function theShopLogsTheTransactionWithMosaic($location)
    {
        throw new PendingException(
           'How?'
        );
    }
}

