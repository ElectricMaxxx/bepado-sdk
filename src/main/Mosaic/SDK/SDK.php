<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK;

use Mosaic\Common\Rpc;
use Mosaic\Common\Struct\RpcCall;

/**
 * Central SDK class, which serves as an etnry point and service provider.
 *
 * Register your gateway and product handlers here. All calls should be
 * dispatched to this class. It constructs the required helper classes as
 * required.
 *
 * @version $Revision$
 * @api
 */
class SDK
{
    /**
     * Gateway to custom storage
     *
     * @var Gateway
     */
    protected $gateway;

    /**
     * Product importer
     *
     * @var ProductImporter
     */
    protected $importer;

    /**
     * Product provider
     *
     * @var ProductProvider
     */
    protected $provider;

    /**
     * Service registry
     *
     * @var Rpc\ServiceRegistry
     */
    protected $registry;

    /**
     * Call marshaller
     *
     * @var Rpc\Marshaller\CallMarshaller
     */
    protected $marshaller;

    /**
     * Call unmarshaller
     *
     * @var Rpc\Marshaller\CallUnmarshaller
     */
    protected $unmarshaller;

    /**
     * Shopping service
     *
     * @var Service\Shopping
     */
    protected $shoppingService;

    /**
     * Verificator dispatcher
     *
     * @var Struct\VerificatorDispatcher
     */
    protected $verificator;

    /**
     * Revision provider
     *
     * @var RevisionProvider
     */
    protected $revisionProvider;

    public function __construct(
        Gateway $gateway,
        ProductImporter $importer,
        ProductProvider $provider
    ) {
        $this->gateway = $gateway;
        $this->importer = $importer;
        $this->provider = $provider;
    }

    /**
     * Handle request XML
     *
     * handle the XML encoding the web service request. Returns XML building
     * the response.
     *
     * @param string $xml
     * @return string
     */
    public function handle($xml)
    {
        return $this->getMarshaller()->marshal(
            new RpcCall(
                array(
                    'service' => 'null',
                    'command' => 'return',
                    'arguments' => array($this->getServiceRegistry()->dispatch(
                        $this->getUnmarshaller()->unmarshal($xml)
                    ))
                )
            )
        );
    }

    /**
     * Check products still are in the state they are stored locally
     *
     * This method will verify with the remote shops that products are still in
     * the expected state. If the state of products changed this method will
     * return a Struct\Message, which should be ACK'ed by the user. Otherwise
     * this method will just return true.
     *
     * If data updated are detected, the local product database will be updated
     * accordingly.
     *
     * This method is a convenience method to check the state of a set of
     * remote products. The state will be checked again during
     * reserveProducts().
     *
     * @param Struct\Order $order
     * @return mixed
     */
    public function checkProducts(Struct\Order $order)
    {
        $this->getVerificator()->verify($order);
        $this->getShoppingService()->checkProducts($order);
    }

    /**
     * Reserve products
     *
     * This method will reserve the given products in the remote shops.
     *
     * If the product data change in a relevant way, this method will not
     * reserve the products, but instead return a Struct\Message, which should
     * be ACK'ed by the user. Afterwards another reservation may be issued.
     *
     * If The reservation of the product set succeeded a hash of reservation
     * IDs for all involved shops will be returned. This hash must be stored in
     * the shop for all further transactions. The session is probably the best
     * location for this.
     *
     * If data updated are detected, the local product database will be updated
     * accordingly.
     *
     * @param Struct\Order $order
     * @return mixed
     */
    public function reserveProducts(Struct\Order $order)
    {
        $this->getVerificator()->verify($order);
        throw new \RuntimeException('@TODO: Implement');
    }

    /**
     * Checkout product sets related to the given reservation IDs
     *
     * This process is the final "buy" transaction. It should be part of the
     * checkout process and be handled synchronously.
     *
     * This method will just return true, if the transaction worked as
     * expected. If it failed, or partially failed, a corresponding
     * Struct\Message will be returned.
     *
     * @param string[] $products
     * @return mixed
     */
    public function checkout(array $reservationIDs)
    {
        throw new \RuntimeException('@TODO: Implement');
    }

    public function getUnmarshaller()
    {
        if ($this->unmarshaller === null) {
            $this->unmarshaller = new Rpc\Marshaller\CallUnmarshaller\XmlCallUnmarshaller();
        }

        return $this->unmarshaller;
    }

    public function getMarshaller()
    {
        if ($this->marshaller === null) {
            $this->marshaller = new Rpc\Marshaller\CallMarshaller\XmlCallMarshaller();
        }

        return $this->marshaller;
    }

    public function getServiceRegistry()
    {
        if ($this->registry === null) {
            $this->registry = new Rpc\ServiceRegistry();
            $this->registry->registerService(
                'products',
                array('export', 'import', 'getLastRevision'),
                new Service\Product(
                    $this->gateway,
                    $this->importer
                )
            );
        }

        return $this->registry;
    }

    public function getVerificator()
    {
        if ($this->verificator === null) {
            $this->verificator = new Struct\VerificatorDispatcher(
                array(
                    '\\Mosaic\\SDK\\Struct\\Order'     => new Struct\Verificator\Order(),
                    '\\Mosaic\\SDK\\Struct\\OrderItem' => new Struct\Verificator\OrderItem(),
                    '\\Mosaic\\SDK\\Struct\\Product'   => new Struct\Verificator\Product(),
                )
            );
        }

        return $this->verificator;
    }

    public function getShoppingService()
    {
        if ($this->shoppingService === null) {
            $this->shoppingService = new Service\Shopping(
                new ShopFactory()
            );
        }

        return $this->shoppingService;
    }

    public function getRevisionProvider()
    {
        if ($this->revisionProvider === null) {
            $this->revisionProvider = new RevisionProvider\Time();
        }

        return $this->revisionProvider;
    }
}
