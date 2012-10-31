<?php

namespace Mosaic\SDK;

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Mosaic\SdkApi\Struct\Product;
use Mosaic\Common\Rpc;
use Mosaic\Common\Struct;

use \PHPUnit_Framework_Assert as Assertion;

/**
 * Features context.
 */
class ProductImportContext extends BehatContext
{
    /**
     * Gateway
     *
     * @var Gateway
     */
    protected $gateway;

    /**
     * Rpc service registry
     *
     * @var Rpc\ServiceRegistry
     */
    protected $registry;

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
    protected $modifiedProductCount;

    /**
     * Revision provider
     *
     * @var RevisionProvider
     */
    protected $revisionProvider;

    public function __construct()
    {
        $this->initDatabase();
        $this->initController();

        $this->revisionProvider = new RevisionProvider\Time();
    }

    protected function initDatabase()
    {
        $config = @parse_ini_file(__DIR__ . '/../../../build.properties');
        $this->gateway = new Gateway\MySQLi($connection = new MySQLi(
            $config['db.hostname'],
            $config['db.userid'],
            $config['db.password'],
            $config['db.name']
        ));
        $connection->query('TRUNCATE TABLE mosaic_change;');
        $connection->query('TRUNCATE TABLE mosaic_product;');
    }

    protected function initController()
    {
        $this->service = new Rpc\ServiceRegistry();
        $this->service->registerService(
            'products',
            array('export'),
            new Service\Product($this->gateway)
        );

        $this->controller = new Controller(
            $this->service,
            new Rpc\Marshaller\CallUnmarshaller\XmlCallUnmarshaller(),
            new Rpc\Marshaller\CallMarshaller\XmlCallMarshaller(
                new \Mosaic\Common\XmlHelper()
            )
        );
    }

    /**
     * @Given /^I have (\d+) products in my shop$/
     */
    public function iHaveProductsInMyShop($productCount)
    {
        $this->modifiedProductCount += $productCount;
        for ($i = 0; $i < $productCount; ++$i) {
            $this->gateway->recordInsert(
                new Product(array(
                    'shopId' => 'shop',
                    'sourceId' => 'product-' . $i,
                )),
                $this->revisionProvider->next()
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
            $this->gateway->recordUpdate(
                new Product(array(
                    'shopId' => 'shop',
                    'sourceId' => 'product-' . $i,
                )),
                $this->revisionProvider->next()
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
            $this->gateway->recordDelete(
                new Product(array(
                    'shopId' => 'shop',
                    'sourceId' => 'product-' . $i,
                )),
                $this->revisionProvider->next()
            );
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
            $changes = $this->service->dispatch(
                new Struct\RpcCall(array(
                    'service' => 'products',
                    'command' => 'export',
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
            Assertion::assertEquals("delete", $change->operation);
        }
    }

    /**
     * @Given /^I add (\d+) products$/
     */
    public function iAddProducts($productCount)
    {
        throw new PendingException();
    }
}

