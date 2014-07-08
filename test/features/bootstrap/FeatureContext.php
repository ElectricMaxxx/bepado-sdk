<?php

use Behat\Behat\Context\ClosuredContextInterface;
use Behat\Behat\Context\TranslatedContextInterface;
use Behat\Behat\Context\BehatContext;
use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Event\ScenarioEvent;

use Bepado\SDK\MySQLi;

require __DIR__ . '/../../../vendor/autoload.php';

require __DIR__ . '/FromShopContext.php';
require __DIR__ . '/ToShopContext.php';
require __DIR__ . '/ShopPurchaseContext.php';

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $this->useContext('fromShop', new \Bepado\SDK\FromShopContext());
        $this->useContext('toShop', new \Bepado\SDK\ToShopContext());
        $this->useContext('shippingCosts', new \Bepado\SDK\ShippingCostsContext());
        $this->useContext('shopPurchase', new \Bepado\SDK\ShopPurchaseContext());
        $this->useContext('category', new \Bepado\SDK\CategoryContext());
    }

    /**
     * @BeforeScenario
     */
    public function setupDatabase(ScenarioEvent $event)
    {
        $connection = $this->createConnection();
        foreach ($this->getSubcontexts() as $context) {
            $context->initSDK($connection);
        }
    }

    private function createConnection()
    {
        $config = @parse_ini_file(__DIR__ . '/../../../build.properties');
        $storage = getenv('STORAGE') ?: 'InMemory';

        switch ($storage) {
            case 'MySQLi':
                $connection = new MySQLi(
                    $config['db.hostname'],
                    $config['db.userid'],
                    $config['db.password'],
                    $config['db.name']
                );
                break;

            case 'PDO':
                $connection = new \PDO(
                    sprintf(
                        'mysql:dbname=%s;host=%s',
                        $config['db.name'],
                        $config['db.hostname']
                    ),
                    $config['db.userid'],
                    $config['db.password']
                );
                $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                break;

            default:
                $connection = null;
                break;
        }

        if ($connection) {
            $connection->query('TRUNCATE TABLE bepado_change;');
            $connection->query('TRUNCATE TABLE bepado_product;');
            $connection->query('TRUNCATE TABLE bepado_data;');
            $connection->query('TRUNCATE TABLE bepado_reservations;');
            $connection->query('TRUNCATE TABLE bepado_shipping_costs;');
        }

        return $connection;
    }
}
