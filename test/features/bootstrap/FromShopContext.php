<?php

namespace Mosaic\SDK;

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Mosaic\SDK\Struct\Product;
use Mosaic\SDK\Struct\Change;
use Mosaic\Common\Rpc;
use Mosaic\Common\Struct;

use \PHPUnit_Framework_Assert as Assertion;

require_once __DIR__ . '/SDKContext.php';

/**
 * Features context.
 */
class FromShopContext extends SDKContext
{
    /**
     * Product processing offset
     *
     * @var int
     */
    protected $offset;

    /**
     * Configured amount of products to fetch per interval
     *
     * @var int
     */
    protected $productsPerInterval;

    /**
     * Last sync revision
     *
     * @var string
     */
    protected $lastRevision;

    /**
     * Count of products which are overall affected by change
     *
     * @var int
     */
    protected $modifiedProductCount = 0;

    /**
     * Incrementally update product ID
     *
     * @var int
     */
    protected $productId = 1;

    /**
     * Revision provider
     *
     * @var RevisionProvider
     */
    protected $revisionProvider;

    public function __construct()
    {
        parent::__construct();

        $this->revisionProvider = new RevisionProvider\Time();
    }

    /**
     * @Given /^I have (\d+) products in my shop$/
     * @Given /^I add (\d+) products$/
     */
    public function iHaveProductsInMyShop($productCount)
    {
        $end = $this->productId + $productCount;
        $this->modifiedProductCount += $productCount;
        for (; $this->productId < $end; ++$this->productId) {
            $this->sdk->recordInsert(
                $this->getProduct($this->productId)
            );
        }
    }

    /**
     * @Given /^I update (\d+) products$/
     */
    public function iUpdateProducts($productCount)
    {
        $this->modifiedProductCount += $productCount;
        for ($i = 0; $i < $productCount; ++$i) {
            $this->sdk->recordUpdate(
                $this->getProduct($this->productId, 'update')
            );
        }
    }

    /**
     * @Given /^I remove (\d+) products$/
     */
    public function iRemoveProducts($productCount)
    {
        $this->modifiedProductCount += $productCount;
        for ($i = 0; $i < $productCount; ++$i) {
            $this->sdk->recordDelete($i);
        }
    }

    /**
     * @Given /^I configured the update interval to (\d+) products per hour$/
     */
    public function iConfiguredTheUpdateIntervalToProductsPerHour($productCount)
    {
        $this->offset = 1;
        $this->productsPerInterval = $productCount;
    }

    /**
     * @When /^Import is triggered(?: for the (\d+)\. time)?$/
     */
    public function importIsTriggeredForTheNthTime($iteration = 1)
    {
        $this->offset = $iteration;
    }

    protected function syncChanges()
    {
        $overallProductCount = 0;
        for ($i = 0; $i < $this->offset; ++$i) {
            $changes = $this->makeRpcCall(
                new Struct\RpcCall(array(
                    'service' => 'products',
                    'command' => 'fromShop',
                    'arguments' => array(
                        $this->lastRevision,
                        $this->productsPerInterval
                    )
                ))
            );

            $overallProductCount += count($changes);
            if (count($changes)) {
                $this->lastRevision = end($changes)->revision;
            }
        }

        return $changes;
    }

    /**
     * @Then /^(\d+) products are synchronized$/
     */
    public function productsAreSynchronized($productCount)
    {
        $changes = $this->syncChanges();
        Assertion::assertEquals($productCount, count($changes));

        foreach ($changes as $change) {
            $this->dependencies->getVerificator()->verify($change);
        }
    }

    /**
     * @Then /^All products are synchronized$/
     */
    public function allProductsAreSynchronized()
    {
        $changes = $this->syncChanges();
        Assertion::assertEquals(
            $this->modifiedProductCount,
            count($changes) + (($this->offset - 1) * $this->productsPerInterval)
        );

        foreach ($changes as $change) {
            $this->dependencies->getVerificator()->verify($change);
        }
    }

    /**
     * @Given /^All products are already syncronized$/
     */
    public function allProductsAreAlreadySyncronized()
    {
        while (count($this->syncChanges()));
    }

    /**
     * @Then /^(\d+) products are deleted$/
     */
    public function productsAreDeleted($productCount)
    {
        $changes = $this->syncChanges();

        Assertion::assertEquals($productCount, count($changes));
        foreach ($changes as $change) {
            $this->dependencies->getVerificator()->verify($change);
            Assertion::assertTrue($change instanceof Change\FromShop\Delete);
        }
    }
}
