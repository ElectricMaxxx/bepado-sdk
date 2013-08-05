<?php

use Behat\Behat\Context\ClosuredContextInterface;
use Behat\Behat\Context\TranslatedContextInterface;
use Behat\Behat\Context\BehatContext;
use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

// ABC .pear directory location
$pearDirectory = __DIR__ . '/../../../../.pear/pear/php/';
set_include_path(get_include_path() . PATH_SEPARATOR . $pearDirectory);

require __DIR__ . '/../../../vendor/autoload.php';

require __DIR__ . '/FromShopContext.php';
require __DIR__ . '/ToShopContext.php';
require __DIR__ . '/ShopPurchaseContext.php';

require_once 'PHPUnit/Autoload.php';

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
        $this->useContext('shopPurchase', new \Bepado\SDK\ShopPurchaseContext());
    }
}
