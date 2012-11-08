<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

require __DIR__ . '/../../../vendor/autoload.php';

require __DIR__ . '/FromShopContext.php';
require __DIR__ . '/ToShopContext.php';
require __DIR__ . '/ShopPurchaseContext.php';
require __DIR__ . '/PushShopConfigContext.php';

require_once __DIR__ . '/../../../../.pear/pear/php/PHPUnit/Autoload.php';

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
        $this->useContext('fromShop', new \Mosaic\SDK\FromShopContext());
        $this->useContext('toShop', new \Mosaic\SDK\ToShopContext());
        $this->useContext('shopPurchase', new \Mosaic\SDK\ShopPurchaseContext());
        $this->useContext('pushShopConfig', new \Mosaic\SDK\PushShopConfigContext());
    }
}

