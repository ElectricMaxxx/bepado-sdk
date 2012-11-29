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

use \PHPUnit_Framework_MockObject_Generator as Mocker;
use \PHPUnit_Framework_MockObject_Matcher_AnyInvokedCount as AnyInvokedCount;
use \PHPUnit_Framework_MockObject_Matcher_InvokedAtIndex as InvokedAt;
use \PHPUnit_Framework_MockObject_Stub_Return as ReturnValue;

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
     * Result of last processing
     *
     * @var mixed
     */
    protected $result;

    /**
     * Currently used mock for to shop gateway
     *
     * @var ProductToShop
     */
    protected $productToShop;

    /**
     * Currently used mock for from shop gateway
     *
     * @var ProductFromShop
     */
    protected $productFromShop;

    /**
     * Currently used mock for logger
     *
     * @var Logger
     */
    protected $logger;

    protected function initSDK()
    {
        $this->productToShop = Mocker::getMock('\\Mosaic\\SDK\\ProductToShop');
        $this->productFromShop = Mocker::getMock('\\Mosaic\\SDK\\ProductFromShop');
        $this->logger = Mocker::getMock('\\Mosaic\\SDK\\Logger');

        $this->sdk = new SDK(
            $this->getGateway(),
            $this->productToShop,
            $this->productFromShop
        );

        // Inject custom direct access shop gateway factory
        $shoppingServiceProperty = new \ReflectionProperty(get_class($this->sdk), 'shoppingService');
        $shoppingServiceProperty->setAccessible(true);
        $shoppingServiceProperty->setValue(
            $this->sdk,
            new Service\Shopping(
                new ShopFactory\DirectAccess(
                    $this->productToShop,
                    $this->productFromShop,
                    $this->logger
                ),
                new ChangeVisitor\Message(
                    $this->sdk->getVerificator()
                )
            )
        );

        // Inject custom logger
        $loggerProperty = new \ReflectionProperty(get_class($this->sdk), 'logger');
        $loggerProperty->setAccessible(true);
        $loggerProperty->setValue(
            $this->sdk,
            $this->logger
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
     * @Given /^The products? (?:is|are) available in (\d+) shops?$/
     */
    public function theProductIsAvailableInNShops($shops)
    {
        for ($i = 1; $i <= $shops; ++$i) {
            $this->productFromShop
                ->expects(new InvokedAt(($i - 1) * 2))
                ->method('getProducts')
                ->will(new ReturnValue(
                    array(
                        new Struct\Product(
                            array(
                                'shopId' => 'shop-' . $i,
                                'sourceId' => '23-' . $i,
                                'price' => 42.23,
                                'currency' => 'EUR',
                                'availability' => 5,
                                'title' => 'Sindelfingen',
                            )
                        ),
                    )
                ));
        }
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
                    'country' => 'Germany',
                )
            );
        }

        $this->order->products[] = new Struct\OrderItem(
            array(
                'count' => 1,
                'product' => new Struct\Product(
                    array(
                        'shopId' => 'shop-' . $remoteShop,
                        'sourceId' => '23-' . $remoteShop,
                        'price' => 42.23,
                        'currency' => 'EUR',
                        'availability' => 5,
                        'title' => 'Sindelfingen',
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
        $this->result = $this->sdk->reserveProducts($this->order);
        $this->sdk->getVerificator()->verify($this->result);

        if (!count($this->result->messages)) {
            $this->result = $this->sdk->checkout($this->result->reservationIDs);
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
        $this->productFromShop
            ->expects(new InvokedAt(0))
            ->method('getProducts')
            ->with(array('23-1'))
            ->will(new ReturnValue(
                array(
                    new Struct\Product(
                        array(
                            'shopId' => 'shop-1',
                            'sourceId' => '23-1',
                            'price' => 42.23,
                            'currency' => 'EUR',
                            'availability' => 0,
                            'title' => 'Sindelfingen',
                        )
                    ),
                )
            ));
    }

    /**
     * @Given /^The product data is still valid$/
     */
    public function theProductDataIsStillValid()
    {
        $this->productFromShop
            ->expects(new AnyInvokedCount())
            ->method('getProducts')
            ->with(array('23-1'))
            ->will(new ReturnValue(
                array(
                    new Struct\Product(
                        array(
                            'shopId' => 'shop-1',
                            'sourceId' => '23-1',
                            'price' => 42.23,
                            'currency' => 'EUR',
                            'availability' => 5,
                            'title' => 'Sindelfingen',
                        )
                    ),
                )
            ));
    }

    /**
     * @When /^The Customer views the order overview$/
     */
    public function theCustomerViewsTheOrderOverview()
    {
        $this->result = $this->sdk->checkProducts($this->order);

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
                new Struct\Message(array(
                    'message' => 'Availability of product %product changed to %availability.',
                    'values' => array(
                        'product' => 'Sindelfingen',
                        'availability' => 0,
                    ),
                ))
            ),
            $this->result
        );
    }

    /**
     * @Given /^The product price has changed in the remote shop$/
     */
    public function theProductPriceHasChangedInTheRemoteShop()
    {
        $this->productFromShop
            ->expects(new InvokedAt(0))
            ->method('getProducts')
            ->with(array('23-1'))
            ->will(new ReturnValue(
                array(
                    new Struct\Product(
                        array(
                            'shopId' => 'shop-1',
                            'sourceId' => '23-1',
                            'price' => 45.23,
                            'currency' => 'EUR',
                            'availability' => 5,
                            'title' => 'Sindelfingen',
                        )
                    ),
                )
            ));
    }

    /**
     * @Then /^The customer is informed about the changed price$/
     */
    public function theCustomerIsInformedAboutTheChangedPrice()
    {
        Assertion::assertEquals(
            array(
                new Struct\Message(array(
                    'message' => 'Price of product %product changed to %price.',
                    'values' => array(
                        'product' => 'Sindelfingen',
                        'price' => 45.23,
                    ),
                ))
            ),
            $this->result
        );
    }

    /**
     * @Then /^The product is reserved in the remote shop$/
     */
    public function theProductIsReservedInTheRemoteShop()
    {
        Assertion::assertTrue($this->result instanceof Struct\Reservation);
        Assertion::assertEquals(0, count($this->result->messages));
        Assertion::assertEquals(1, count($this->result->reservationIDs));
    }

    /**
     * @Given /^The product changes availability between check and purchase$/
     */
    public function theProductChangesAvailabilityBetweenCheckAndPurchase()
    {
        $this->productFromShop
            ->expects(new InvokedAt(0))
            ->method('getProducts')
            ->with(array('23-1'))
            ->will(new ReturnValue(
                array(
                    new Struct\Product(
                        array(
                            'shopId' => 'shop-1',
                            'sourceId' => '23-1',
                            'price' => 42.23,
                            'currency' => 'EUR',
                            'availability' => 0,
                            'title' => 'Sindelfingen',
                        )
                    ),
                )
            ));
    }

    /**
     * @Given /^The buy process fails and customer is informed about this$/
     */
    public function theBuyProcessFailsAndTheCustomerIsInformedAboutThis()
    {
        Assertion::assertTrue($this->result instanceof Struct\Reservation);
        $this->sdk->getVerificator()->verify($this->result);
        Assertion::assertNotEquals(0, count($this->result->messages));
    }

    /**
     * @Given /^The reservation was lost$/
     */
    public function theReservationWasLost()
    {
        throw new PendingException('Make reservation fail.');
    }

    /**
     * @Given /^The buy process fails$/
     */
    public function theBuyProcessFails()
    {
        throw new PendingException();
    }

    /**
     * @Then /^The (local|remote) shop logs the transaction with Mosaic$/
     */
    public function theShopLogsTheTransactionWithMosaic($location)
    {
        throw new PendingException(
           'How?'
        );
    }
}

