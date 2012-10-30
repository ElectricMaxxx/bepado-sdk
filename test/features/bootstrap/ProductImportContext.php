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

    public function __construct()
    {
        $this->initDatabase();
        $this->initController();
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
        $connection->query('TRUNCATE TABLE changes;');
    }

    protected function initController()
    {
        $this->controller = new Controller(
            new Rpc\ServiceRegistry(),
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
        for ($i = 0; $i < $productCount; ++$i) {
            $this->gateway->recordInsert(new Product(array(
                'shopId' => 'shop',
                'sourceId' => 'product-' . $i,
            )));
        }
    }

    /**
     * @Given /^I configured the update interval to (\d+) products per hour$/
     */
    public function iConfiguredTheUpdateIntervalToProductsPerHour($productCount)
    {
        $this->offset = 0;
        $this->productsPerInterval = $productCount;
    }

    /**
     * @When /^Import is triggered for the (\d+)\. time$/
     */
    public function importIsTriggeredForTheTime($iteration)
    {
        $this->offset = ( $iteration - 1 ) * $this->productsPerInterval;
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

