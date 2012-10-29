<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

/**
 * Features context.
 */
class ProductImportContext extends BehatContext
{
    /**
     * @Given /^I have (\d+) products in my shop$/
     */
    public function iHaveProductsInMyShop($productCount)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I configured the update interval to (\d+) products per hour$/
     */
    public function iConfiguredTheUpdateIntervalToProductsPerHour($interval)
    {
        throw new PendingException();
    }

    /**
     * @When /^Import is triggered for the (\d+)\. time$/
     */
    public function importIsTriggeredForTheTime($iteration)
    {
        throw new PendingException();
    }

    /**
     * @Then /^(\d+) products are synchronized$/
     */
    public function productsAreSynchronized($productCount)
    {
        throw new PendingException();
    }

    /**
     * @Then /^All products are synchronized$/
     */
    public function allProductsAreSynchronized()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I update (\d+) products after the (\d+)\. run$/
     */
    public function iUpdateProductsAfterTheRun($productCount, $iteration)
    {
        throw new PendingException();
    }

    /**
     * @Given /^All products are already syncronized$/
     */
    public function allProductsAreAlreadySyncronized()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I update (\d+) products$/
     */
    public function iUpdateProducts($productCount)
    {
        throw new PendingException();
    }

    /**
     * @When /^Import is triggered$/
     */
    public function importIsTriggered()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I remove (\d+) products$/
     */
    public function iRemoveProducts($productCount)
    {
        throw new PendingException();
    }

    /**
     * @Then /^(\d+) products are deleted$/
     */
    public function productsAreDeleted($productCount)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I add (\d+) products$/
     */
    public function iAddProducts($productCount)
    {
        throw new PendingException();
    }
}

