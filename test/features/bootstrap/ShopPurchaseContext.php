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
     * Urrently processed order
     *
     * @var Struct\Order
     */
    protected $order;

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
        throw new PendingException();
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
        throw new PendingException();
    }

    /**
     * @Then /^The customer will receive the products?$/
     */
    public function theCustomerWillReceiveTheProducts()
    {
        throw new PendingException();
    }

    /**
     * @Given /^The product is not available in remote shop$/
     */
    public function theProductIsNotAvailableInRemoteShop()
    {
        throw new PendingException();
    }

    /**
     * @When /^The Customer views the order overview$/
     */
    public function theCustomerViewsTheOrderOverview()
    {
        $this->controller->checkProducts($this->order);
    }

    /**
     * @Then /^The customer is informed about the unavailability$/
     */
    public function theCustomerIsInformedAboutTheUnavailability()
    {
        throw new PendingException();
    }

    /**
     * @Given /^The product price has changed in the remote shop$/
     */
    public function theProductPriceHasChangedInTheRemoteShop()
    {
        throw new PendingException();
    }

    /**
     * @Then /^The customer is informed about the changed price$/
     */
    public function theCustomerIsInformedAboutTheChangedPrice()
    {
        throw new PendingException();
    }

    /**
     * @Then /^The product is reserved in the remote shop$/
     */
    public function theProductIsReservedInTheRemoteShop()
    {
        throw new PendingException();
    }

    /**
     * @When /^The customer completes the checkout$/
     */
    public function theCustomerCompletesTheCheckout()
    {
        throw new PendingException();
    }

    /**
     * @Given /^The buy process fails$/
     */
    public function theBuyProcessFails()
    {
        throw new PendingException();
    }

    /**
     * @Then /^The customer is informed about this\.$/
     */
    public function theCustomerIsInformedAboutThis()
    {
        throw new PendingException();
    }

    /**
     * @Then /^The (local|remote) shop logs the transaction with Mosaic$/
     */
    public function theShopLogsTheTransactionWithMosaic($location)
    {
        throw new PendingException();
    }
}

