<?php

namespace Mosaic\SDK;

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

/**
 * Features context.
 */
class ShopPurchaseContext extends BehatContext
{
    /**
     * @Given /^The Product is listed as available$/
     */
    public function theProductIsListedAsAvailable()
    {
        throw new PendingException();
    }

    /**
     * @Given /^A customer adds a product from a remote shop to basket$/
     */
    public function aCustomerAddsAProductFromARemoteShopToBasket()
    {
        throw new PendingException();
    }

    /**
     * @When /^The Customer checks out$/
     */
    public function theCustomerChecksOut()
    {
        throw new PendingException();
    }

    /**
     * @Then /^The customer will receive the product$/
     */
    public function theCustomerWillReceiveTheProduct()
    {
        throw new PendingException();
    }

    /**
     * @Given /^The Product is not available in remote shop$/
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
        throw new PendingException();
    }

    /**
     * @Then /^The customer is informed about the unavailability$/
     */
    public function theCustomerIsInformedAboutTheUnavailability()
    {
        throw new PendingException();
    }

    /**
     * @Given /^The Product price has changed in the remote shop$/
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

