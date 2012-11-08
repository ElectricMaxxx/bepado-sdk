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
     * Verificator dispatcher
     *
     * @var Struct\VerificatorDispatcher
     */
    protected $verificator;

    /**
     * Shopping service
     *
     * @var Service\Shopping
     */
    protected $shoppingService;

    /**
     * Sync service
     *
     * @var Service\Syncer
     */
    protected $syncService;

    /**
     * Product hasher
     *
     * @var ProductHasher
     */
    protected $productHasher;

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
     * Sync changes feed
     *
     * Evaluates which products are new in the shop database and marks those
     * products for the export. Results in new inserts, updates and deletes in
     * the changes feed.
     *
     * Use this method, if your shop is not able to record all change
     * operations on your products itself, using the record*() methods.
     *
     * @return void
     */
    public function sync()
    {
        $this->getSyncService()->sync();
    }

    /**
     * Record product insert
     *
     * Establish a hook in your shop and call this method for every new
     * product, which should be exported to Mosaic.
     *
     * @param string $id
     * @param string $hash
     * @param string $revision
     * @param Struct\Product $product
     * @return void
     */
    public function recordInsert($id, $hash, $revision, Struct\Product $product)
    {
        $this->getVerificator()->verify($product);
        $this->gateway->recordInsert($id, $hash, $revision, $product);
    }

    /**
     * Record product update
     *
     * Establish a hook in your shop and call this method for every update of a
     * product, which is exported to Mosaic.
     *
     * @param string $id
     * @param string $hash
     * @param string $revision
     * @param Struct\Product $product
     * @return void
     */
    public function recordUpdate($id, $hash, $revision, Struct\Product $product)
    {
        $this->getVerificator()->verify($product);
        $this->gateway->recordUpdate($id, $hash, $revision, $product);
    }

    /**
     * Record product delete
     *
     * Establish a hook in your shop and call this method for every delete of a
     * product, which is exported to Mosaic.
     *
     * @param string $id
     * @param string $revision
     * @return void
     */
    public function recordDelete($id, $revision)
    {
        $this->gateway->recordDelete($id, $revision);
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

    /**
     * @private
     * @return Rpc\Marshaller\CallUnmarshaller
     */
    public function getUnmarshaller()
    {
        if ($this->unmarshaller === null) {
            $this->unmarshaller = new Rpc\Marshaller\CallUnmarshaller\XmlCallUnmarshaller();
        }

        return $this->unmarshaller;
    }

    /**
     * @private
     * @return Rpc\Marshaller\CallMarshaller
     */
    public function getMarshaller()
    {
        if ($this->marshaller === null) {
            $this->marshaller = new Rpc\Marshaller\CallMarshaller\XmlCallMarshaller();
        }

        return $this->marshaller;
    }

    /**
     * @private
     * @return Gateway
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * @private
     * @return Rpc\ServiceRegistry
     */
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

    /**
     * @private
     * @return VerificatorDispatcher
     */
    public function getVerificator()
    {
        if ($this->verificator === null) {
            $this->verificator = new Struct\VerificatorDispatcher(
                array(
                    'Mosaic\\SDK\\Struct\\Order' =>
                         new Struct\Verificator\Order(),
                    'Mosaic\\SDK\\Struct\\OrderItem' =>
                         new Struct\Verificator\OrderItem(),
                    'Mosaic\\SDK\\Struct\\Product' =>
                         new Struct\Verificator\Product(),
                    'Mosaic\\SDK\\Struct\\Change\\FromShop\\Insert' =>
                         new Struct\Verificator\Change(),
                    'Mosaic\\SDK\\Struct\\Change\\FromShop\\Update' =>
                         new Struct\Verificator\Change(),
                    'Mosaic\\SDK\\Struct\\Change\\FromShop\\Delete' =>
                         new Struct\Verificator\Change(),
                    'Mosaic\\SDK\\Struct\\Change\\ToShop\\InsertOrUpdate' =>
                         new Struct\Verificator\Change(),
                    'Mosaic\\SDK\\Struct\\Change\\ToShop\\Delete' =>
                         new Struct\Verificator\Change(),
                )
            );
        }

        return $this->verificator;
    }

    /**
     * @private
     * @return Service\Shopping
     */
    public function getShoppingService()
    {
        if ($this->shoppingService === null) {
            $this->shoppingService = new Service\Shopping(
                new ShopFactory()
            );
        }

        return $this->shoppingService;
    }

    /**
     * @private
     * @return Service\Syncer
     */
    public function getSyncService()
    {
        if ($this->syncService === null) {
            $this->syncService = new Service\Syncer(
                $this->getGateway(),
                $this->provider,
                $this->getRevisionProvider(),
                $this->getProductHasher()
            );
        }

        return $this->syncService;
    }

    /**
     * @private
     * @return ProductHasher
     */
    public function getProductHasher()
    {
        if ($this->productHasher === null) {
            $this->productHasher = new ProductHasher\Simple();
        }

        return $this->productHasher;
    }

    /**
     * @private
     * @return RevisionProvider
     */
    public function getRevisionProvider()
    {
        if ($this->revisionProvider === null) {
            $this->revisionProvider = new RevisionProvider\Time();
        }

        return $this->revisionProvider;
    }
}
