<?php

namespace Bepado\SDK;

use Behat\Behat\Context\ClosuredContextInterface;
use Behat\Behat\Context\TranslatedContextInterface;
use Behat\Behat\Context\BehatContext;
use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Bepado\SDK\Struct;
use Bepado\SDK\Controller;
use Bepado\SDK\ShippingCostCalculator;
use Bepado\SDK\ShippingCosts\Rule;
use Bepado\SDK\ShippingCosts\Rules;
use Bepado\SDK\ErrorHandler;
use Bepado\SDK\RPC;

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
     * @var bool
     */
    protected $fixedPriceItems = true;

    /**
     * Result of last processing
     *
     * @var mixed
     */
    protected $result;

    /**
     * Currently used mock for logger
     *
     * @var Logger
     */
    protected $logger;

    /**
     * Gateway of the remote SDK
     *
     * @var remoteGateway
     */
    protected $remoteGateway;

    /**
     * @var int
     */
    protected $priceGroupMargin = 0;

    public function initSDK($connection)
    {
        $this->productToShop = \Phake::mock('\\Bepado\\SDK\\ProductToShop');
        $this->productFromShop = \Phake::mock('\\Bepado\\SDK\\ProductFromShop');

        $this->sdk = new SDK(
            'apikey',
            'http://example.com/endpoint',
            $this->gateway = $this->getGateway($connection),
            $this->productToShop,
            $this->productFromShop,
            null,
            new \Bepado\SDK\HttpClient\NoSecurityRequestSigner()
        );

        $dependenciesProperty = new \ReflectionProperty($this->sdk, 'dependencies');
        $dependenciesProperty->setAccessible(true);
        $this->dependencies = $dependenciesProperty->getValue($this->sdk);

        $this->logger = new Logger\Test();

        $shoppingServiceProperty = new \ReflectionProperty($this->dependencies, 'shoppingService');
        $shoppingServiceProperty->setAccessible(true);
        $shoppingServiceProperty->setValue(
            $this->dependencies,
            new Service\Shopping(
                new ShopFactory\DirectAccess(
                    $this->productToShop,
                    $this->productFromShop,
                    $this->remoteGateway = $this->getGateway($connection),
                    $this->logger
                ),
                new ChangeVisitor\Message(
                    $this->dependencies->getVerificator()
                ),
                $this->productToShop,
                $this->logger,
                new ErrorHandler\Null(),
                new ShippingCostCalculator\RuleCalculator($this->gateway),
                $this->gateway
            )
        );

        // Inject custom logger
        $loggerProperty = new \ReflectionProperty($this->dependencies, 'logger');
        $loggerProperty->setAccessible(true);
        $loggerProperty->setValue(
            $this->dependencies,
            $this->logger
        );

        $this->priceGroupMargin = 0;
        $this->distributeShopConfiguration();
    }

    private function distributeShopConfiguration()
    {
        for ($i = 1; $i <= 2; ++$i) {
            $this->gateway->setShopConfiguration(
                'shop-' . $i,
                new Struct\ShopConfiguration(
                    array(
                        'serviceEndpoint' => 'http://shop' . $i . '.example.com/',
                    )
                )
            );

            $rules = new Rules(array(
                'rules' => array(
                    new Rule\FixedPrice(
                        array(
                            'price' => $i * 2,
                        )
                    )
                )
            ));

            $this->gateway->storeShippingCosts('shop-' . $i, 'shop', "", $rules);
            $this->remoteGateway->storeShippingCosts('shop-' . $i, 'shop', "", $rules);
            // for shared state reasons
            $this->gateway->storeShippingCosts('shop-' . $i, 'shop-' . $i, "", $rules);
            $this->remoteGateway->storeShippingCosts('shop-' . $i, 'shop-' . $i, "", $rules);
        }

        $this->remoteGateway->setShopConfiguration(
            'shop',
            new Struct\ShopConfiguration(
                array(
                    'serviceEndpoint' => 'http://shop.example.com/',
                    'priceGroupMargin' => $this->priceGroupMargin,
                )
            )
        );
    }

    /**
     * @Given /^The product is listed as available$/
     */
    public function theProductIsListedAsAvailable()
    {
        // Just do nothing…
    }

    /**
     * @Given /^the local shop receives a price group margin$/
     */
    public function theLocalShopReceivesAPriceGroupMargin()
    {
        $this->priceGroupMargin = 10;
        $this->distributeShopConfiguration();
    }

    /**
     * @Given /^The products? (?:is|are) available in (\d+) shops?$/
     */
    public function theProductIsAvailableInNShops($shops)
    {
        $methodStub = \Phake::when($this->productFromShop)
            ->getProducts(\Phake::anyParameters());

        $products = array();
        for ($i = 1; $i <= $shops; ++$i) {
            $products[] = new Struct\Product(
                array(
                    'shopId' => 'shop-' . $i,
                    'sourceId' => '23-' . $i,
                    'price' => 42.23,
                    'purchasePrice' => 23.42,
                    'fixedPrice' => $this->fixedPriceItems,
                    'currency' => 'EUR',
                    'availability' => 5,
                    'title' => 'Sindelfingen',
                    'categories' => array('/others'),
                )
            );
        }

        // this is "wrong" to always return both products of both shops, but
        // the algorithm doesn't mind and the test then works.
        $methodStub->thenReturn($products);
    }

    /**
     * @Given /^A customer adds a product from remote shop (\d+) to basket$/
     */
    public function aCustomerAddsAProductFromARemoteShopToBasket($remoteShop)
    {
        if (!$this->order) {
            $this->order = new Struct\Order();

            $this->order->deliveryAddress = new Struct\Address(
                array(
                    'name' => 'John Doe',
                    'line1' => 'Foo-Street 42',
                    'zip' => '12345',
                    'city' => 'Sindelfingen',
                    'country' => 'DEU',
                    'email' => 'foo@qafoo.com',
                    'phone' => '+12345678',
                )
            );
        }

        $this->order->orderItems[] = new Struct\OrderItem(
            array(
                'count' => 1,
                'product' => new Struct\Product(
                    array(
                        'shopId' => 'shop-' . $remoteShop,
                        'sourceId' => '23-' . $remoteShop,
                        'price' => 42.23,
                        'purchasePrice' => $this->applyCurrentPriceGroupMargin(23.42),
                        'fixedPrice' => $this->fixedPriceItems,
                        'currency' => 'EUR',
                        'availability' => 5,
                        'title' => 'Sindelfingen',
                        'categories' => array('/others'),
                    )
                ),
            )
        );
    }

    private function applyCurrentPriceGroupMargin($price)
    {
        $discount = $price * $this->priceGroupMargin / 100;
        return ($price - $discount);
    }

    /**
     * @When /^The Customer checks out$/
     */
    public function theCustomerChecksOut()
    {
        $this->result = $this->sdk->reserveProducts($this->order);
        $this->dependencies->getVerificator()->verify($this->result);

        if ($this->result->success) {
            $this->result = $this->sdk->checkout($this->result, 'orderId');
        }
    }

    /**
     * @Then /^The customer will receive the products?$/
     */
    public function theCustomerWillReceiveTheProducts()
    {
        foreach ($this->result as $shopId => $value) {
            Assertion::assertTrue($value);
        }
    }

    /**
     * @Given /^The product is not available in remote shop$/
     */
    public function theProductIsNotAvailableInRemoteShop()
    {
        $methodStub = \Phake::when($this->productFromShop)
            ->getProducts(\Phake::anyParameters())
            ->thenReturn(
                array(
                    new Struct\Product(
                        array(
                            'shopId' => 'shop-1',
                            'sourceId' => '23-1',
                            'price' => 42.23,
                            'purchasePrice' => 23.42,
                            'fixedPrice' => $this->fixedPriceItems,
                            'currency' => 'EUR',
                            'availability' => 0,
                            'title' => 'Sindelfingen',
                            'categories' => array('/others'),
                        )
                    ),
                )
            );
    }

    /**
     * @Given /^The product data is still valid$/
     */
    public function theProductDataIsStillValid()
    {
        $methodStub = \Phake::when($this->productFromShop)
            ->getProducts(\Phake::anyParameters())
            ->thenReturn(
                array(
                    new Struct\Product(
                        array(
                            'shopId' => 'shop-1',
                            'sourceId' => '23-1',
                            'price' => 42.23,
                            'purchasePrice' => 23.42,
                            'fixedPrice' => $this->fixedPriceItems,
                            'currency' => 'EUR',
                            'availability' => 5,
                            'title' => 'Sindelfingen',
                            'categories' => array('/others'),
                        )
                    ),
                )
            );
    }

    /**
     * @When /^The Customer views the order overview$/
     */
    public function theCustomerViewsTheOrderOverview()
    {
        $this->result = $this->sdk->checkProducts(
            array_map(
                function ($orderItem) {
                    return $orderItem->product;
                },
                $this->order->products
            )
        );

        if ($this->result === true) {
            $this->result = $this->sdk->reserveProducts($this->order);
        }
    }

    /**
     * @Then /^The customer is informed about the unavailability$/
     */
    public function theCustomerIsInformedAboutTheUnavailability()
    {
        Assertion::assertEquals(
            array(
                new Struct\Message(
                    array(
                        'message' => 'Availability of product %product changed to %availability.',
                        'values' => array(
                            'product' => 'Sindelfingen',
                            'availability' => 0,
                        ),
                    )
                )
            ),
            $this->result
        );
    }

    /**
     * @Given /^The product (?:price|availability) is updated in the local shop$/
     */
    public function theProductAvailabilityIsUpdatedInTheLocalShop()
    {
        \Phake::verify($this->productToShop)->insertOrUpdate(\Phake::anyParameters());
    }

    /**
     * @Given /^The product was deleted in the remote shop$/
     */
    public function theProductWasDeletedInTheRemoteShop()
    {
        $methodStub = \Phake::when($this->productFromShop)
            ->getProducts(\Phake::anyParameters())
            ->thenReturn(
                array()
            );
    }

    /**
     * @Then /^The customer is informed about the deleted product$/
     */
    public function theCustomerIsInformedAboutTheDeletedProduct()
    {
        Assertion::assertEquals(
            array(
                new Struct\Message(
                    array(
                        'message' => 'Product %product does not exist anymore.',
                        'values' => array(
                            'product' => '23-1',
                        ),
                    )
                )
            ),
            $this->result
        );
    }

    /**
     * @Given /^The product is deleted in the local shop$/
     */
    public function theProductIsDeletedInTheLocalShop()
    {
        \Phake::verify($this->productToShop)->delete(\Phake::anyParameters());
    }

    /**
     * @Given /^The product price has changed in the remote shop$/
     */
    public function theProductPriceHasChangedInTheRemoteShop()
    {
        $methodStub = \Phake::when($this->productFromShop)
            ->getProducts(\Phake::anyParameters())
            ->thenReturn(
                array(
                    new Struct\Product(
                        array(
                            'shopId' => 'shop-1',
                            'sourceId' => '23-1',
                            'price' => 45.23,
                            'purchasePrice' => 23.42,
                            'fixedPrice' => $this->fixedPriceItems,
                            'currency' => 'EUR',
                            'availability' => 5,
                            'title' => 'Sindelfingen',
                            'categories' => array('/others'),
                        )
                    ),
                )
            );
    }

    /**
     * @Then /^The customer is informed about the changed price$/
     */
    public function theCustomerIsInformedAboutTheChangedPrice()
    {
        Assertion::assertEquals(
            array(
                new Struct\Message(
                    array(
                        'message' => 'Price of product %product changed to %price.',
                        'values' => array(
                            'product' => 'Sindelfingen',
                            'price' => 53.82,
                        ),
                    )
                )
            ),
            $this->result
        );
    }

    /**
     * @Then /^The product is reserved in the remote shop$/
     */
    public function theProductIsReservedInTheRemoteShop()
    {
        Assertion::assertTrue($this->result instanceof Struct\Reservation, "Expected a Struct\Reservation object.");
        Assertion::assertTrue($this->result->success, "Result should be success.");
        Assertion::assertEquals(0, count($this->result->messages));
        Assertion::assertEquals(1, count($this->result->orders));
    }

    /**
     * @Given /^The product changes availability between check and purchase$/
     */
    public function theProductChangesAvailabilityBetweenCheckAndPurchase()
    {
        $methodStub = \Phake::when($this->productFromShop)
            ->getProducts(\Phake::anyParameters())
            ->thenReturn(
                array(
                    new Struct\Product(
                        array(
                            'shopId' => 'shop-1',
                            'sourceId' => '23-1',
                            'price' => 42.23,
                            'purchasePrice' => 23.42,
                            'fixedPrice' => $this->fixedPriceItems,
                            'currency' => 'EUR',
                            'availability' => 0,
                            'title' => 'Sindelfingen',
                            'categories' => array('/others'),
                        )
                    ),
                )
            );
    }

    /**
     * @Given /^The buy process fails and customer is informed about this$/
     */
    public function theBuyProcessFailsAndTheCustomerIsInformedAboutThis()
    {
        Assertion::assertTrue($this->result instanceof Struct\Reservation, "Expected a Struct\Reservation object.");
        $this->dependencies->getVerificator()->verify($this->result);
        Assertion::assertFalse($this->result->success, "Result should not be success.");
        Assertion::assertNotEquals(0, count($this->result->messages));
    }

    /**
     * @Given /^The remote shop denies the buy$/
     */
    public function theRemoteShopDeniesTheBuy()
    {
        $methodStub = \Phake::when($this->productFromShop)
            ->buy(\Phake::anyParameters())
            ->thenThrow(
                new \RuntimeException("Buy denied.")
            );
    }

    /**
     * @Given /^The buy process fails$/
     */
    public function theBuyProcessFails()
    {
        foreach ($this->result as $shopId => $value) {
            Assertion::assertFalse($value, "Buy process for $shopId did not fail.");
        }
    }

    /**
     * @Then /^The (local|remote) shop logs the transaction with Bepado$/
     */
    public function theShopLogsTheTransactionWithBepado($location)
    {
        $expectedLogMessage = $location === 'remote' ? 1 : 2;
        $logMessages = $this->logger->getLogMessages();

        Assertion::assertTrue(
            isset($logMessages[$expectedLogMessage]),
            "Expected a $location shop log message, none available."
        );
        Assertion::assertTrue(
            $logMessages[$expectedLogMessage] instanceof Struct\Order,
            "Log message should contain an Order."
        );
    }

    /**
     * @Given /^No transaction is logged$/
     */
    public function noTransactionIsLogged()
    {
        $logMessages = $this->logger->getLogMessages();

        Assertion::assertFalse(
            isset($logMessages[0]),
            "No remote shop transaction logs expected"
        );
        Assertion::assertFalse(
            isset($logMessages[1]),
            "No local shop  transaction logs expected"
        );
    }

    /**
     * @Given /^The (local|remote) shop confirms the transaction with Bepado$/
     */
    public function theShopConfirmsTheTransactionWithBepado($location)
    {
        $expectedLogMessage = $location === 'remote' ? 3 : 4;
        $logMessages = $this->logger->getLogMessages();

        Assertion::assertTrue(
            isset($logMessages[$expectedLogMessage]),
            "Expected a $location shop confirmation, none available."
        );
        Assertion::assertEquals(
            'confirm-' . ($location === 'remote' ? 1 : 2),
            $logMessages[$expectedLogMessage],
            "Log message should contain an confirmation key."
        );
    }

    /**
     * @Given /^No transactions are confirmed$/
     */
    public function noTransactionsAreConfirmed()
    {
        $logMessages = $this->logger->getLogMessages();

        Assertion::assertLessThan(
            4,
            count($logMessages),
            "No confirmation messages expected."
        );
    }

    /**
     * @Given /^The (local|remote) shop transaction logging fails$/
     */
    public function theShopTransactionLoggingFails($location)
    {
        $this->logger->breakOnLogMessage($location === 'remote' ? 1 : 2);
    }

    /**
     * @Given /^The (local|remote) shop transaction confirmation fails$/
     */
    public function theShopTransactionConfirmationFails($location)
    {
        $this->logger->breakOnLogMessage($location === 'remote' ? 3 : 4);
    }

    /**
     * @Given /^The product purchase price has changed in the remote shop$/
     */
    public function theProductPurchasePriceHasChangedInTheRemoteShop()
    {
        $methodStub = \Phake::when($this->productFromShop)
            ->getProducts(\Phake::anyParameters())
            ->thenReturn(
                array(
                    new Struct\Product(
                        array(
                            'shopId' => 'shop-1',
                            'sourceId' => '23-1',
                            'price' => 42.23,
                            'purchasePrice' => 13.37,
                            'fixedPrice' => $this->fixedPriceItems,
                            'currency' => 'EUR',
                            'availability' => 0,
                            'title' => 'Sindelfingen',
                            'categories' => array('/others'),
                        )
                    ),
                )
            );
    }

    /**
     * @Given /^The product purchase price is updated in the local shop$/
     */
    public function theProductPurchasePriceIsUpdatedInTheLocalShop()
    {
        \Phake::verify($this->productToShop)->insertOrUpdate(\Phake::anyParameters());
    }

    /**
     * @Given /^The product does not have a fixed price$/
     */
    public function theProductDoesNotHaveAFixedPrice()
    {
        $this->fixedPriceItems = false;
    }

    /**
     * @Given /^The product shipping costs changed in the remote shop$/
     */
    public function theProductShippingCostsChangedInTheRemoteShop()
    {
        if (!$this->gateway instanceof Gateway\InMemory) {
            throw new PendingException(
                "Since the remote and local shop use the same database for configuration we cannot test this easily with anything but the InMemory gateway. Thus we are skipping this test."
            );
        }

        $rules = new Rules(array(
            'rules' => array(
                new Rule\FixedPrice(
                    array(
                        'price' => .5,
                    )
                )
            )
        ));

        $this->remoteGateway->storeShippingCosts('shop-1', 'shop', 'revision', $rules);
    }

    /**
     * @Then /^The customer is informed about the changed shipping costs$/
     */
    public function theCustomerIsInformedAboutTheChangedShippingCosts()
    {
        Assertion::assertTrue($this->result instanceof Struct\Reservation, "Expected a Struct\Reservation object.");
        Assertion::assertFalse($this->result->success, "Result should not be success.");
        Assertion::assertEquals(
            array(
                'shop-1' => array(
                    new Struct\Message(
                        array(
                            'message' => 'Shipping costs have changed from %oldValue to %newValue.',
                            'values' => array(
                                'oldValue' => '2.38',
                                'newValue' => '0.60',
                            ),
                        )
                    )
                )
            ),
            $this->result->messages
        );
    }

    /**
     * @Given /^The remote shop allows shipping only to "([^"]*)"$/
     */
    public function theRemoteShopAllowsShippingOnlyTo($country)
    {
        $rules = new Rules(array(
            'rules' => array(
                new Rule\CountryDecorator(
                    array(
                        'countries' => array($country),
                        'delegatee' => new Rule\FixedPrice(
                            array(
                                'price' => .5,
                            )
                        )
                    )
                )
            )
        ));

        $this->gateway->storeShippingCosts('shop-1', 'shop', (string)time(), $rules);
        $this->remoteGateway->storeShippingCosts('shop-1', 'shop', (string)time(), $rules);
        // shared state madness
        $this->gateway->storeShippingCosts('shop-1', 'shop-1', (string)time(), $rules);
        $this->remoteGateway->storeShippingCosts('shop-1', 'shop-1', (string)time(), $rules);
    }

    /**
     * @Then /^The Customer is informed about not shippable order$/
     */
    public function theCustomerIsInformedAboutNotShippableOrder()
    {
        Assertion::assertTrue($this->result instanceof Struct\Reservation, "Expected a Struct\Reservation object.");
        Assertion::assertFalse($this->result->success, "Result should not be success.");
        Assertion::assertEquals(
            array(
                'shop-1' => array(new Struct\Message(
                    array( 'message' => 'Products cannot be shipped to %country.',
                        'values' => array(
                            'country' => 'DEU',
                        ),
                    )
                ))
            ),
            $this->result->messages
        );
    }
}
