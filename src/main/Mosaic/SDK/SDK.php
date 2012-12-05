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
 * Central SDK class, which serves as an etnry point and service fromShop.
 *
 * Register your gateway and product handlers here. All calls should be
 * dispatched to this class. It constructs the required helper classes as
 * required.
 *
 * @version $Revision$
 * @api
 */
final class SDK
{
    /**
     * API key for this SDK
     *
     * @var string
     */
    protected $apiKey;

    /**
     * API endpoint URL for this SDK
     *
     * @var string
     */
    protected $apiEndpointUrl;

    /**
     * Indicator if the SDK is verified agianst Mosaic
     *
     * @var bool
     */
    private $verified = false;

    /**
     * Gateway to custom storage
     *
     * @var Gateway
     */
    protected $gateway;

    /**
     * Product toShop
     *
     * @var ProductToShop
     */
    protected $toShop;

    /**
     * Product fromShop
     *
     * @var ProductFromShop
     */
    protected $fromShop;

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
     * Verification service
     *
     * @var Service\Verification
     */
    protected $verificationService;

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
     * Revision fromShop
     *
     * @var RevisionProvider
     */
    protected $revisionFromShop;

    /**
     * Logger
     *
     * @var Logger
     */
    protected $logger;

    /**
     * @param string $apiKey,
     * @param Gateway $gateway
     * @param ProductToShop $toShop
     * @param ProductFromShop $fromShop
     */
    public function __construct(
        $apiKey,
        $apiEndpointUrl,
        Gateway $gateway,
        ProductToShop $toShop,
        ProductFromShop $fromShop
    ) {
        $this->apiKey = $apiKey;
        $this->apiEndpointUrl = $apiEndpointUrl;
        $this->gateway = $gateway;
        $this->toShop = $toShop;
        $this->fromShop = $fromShop;
    }

    /**
     * Tries to verify this SDK, if this did not happen yet.
     *
     * Throws an exception if the verification failed and the required data
     * could not be retrieved or verified.
     *
     * @throws \DomainException
     * @return void
     */
    public function verifySdk()
    {
        if ($this->verified ||
            $this->gateway->getShopId() !== false) {
            return;
        }

        $this->getVerificationService()->verify(
            $this->apiKey,
            $this->apiEndpointUrl
        );
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
        $this->verifySdk();
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
        $this->verifySdk();
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
        $this->verifySdk();
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
        $this->verifySdk();
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
        $this->verifySdk();
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
        $this->verifySdk();
        $this->getVerificator()->verify($order);
        $order->orderShop = $this->gateway->getShopId();
        return $this->getShoppingService()->checkProducts($order);
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
        $this->verifySdk();
        $this->getVerificator()->verify($order);
        $order->orderShop = $this->gateway->getShopId();
        return $this->getShoppingService()->reserveProducts($order);
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
     * @param Struct\Reservation $reservation
     * @return mixed
     */
    public function checkout(Struct\Reservation $reservation)
    {
        $this->verifySdk();
        return $this->getShoppingService()->checkout($reservation);
    }

    /**
     * Get service registry
     *
     * Direct access to this class is provided for testing and verification.
     * Use this to issue a call without the need to craft the corresponding
     * XML.
     *
     * @return Rpc\ServiceRegistry
     */
    public function getServiceRegistry()
    {
        if ($this->registry === null) {
            $this->registry = new Rpc\ServiceRegistry();

            $this->registry->registerService(
                'products',
                array('fromShop', 'toShop', 'getLastRevision'),
                new Service\ProductService(
                    $this->gateway,
                    $this->gateway,
                    $this->toShop
                )
            );

            $this->registry->registerService(
                'configuration',
                array('update'),
                new Service\Configuration(
                    $this->gateway,
                    $this->getVerificator()
                )
            );

            $this->registry->registerService(
                'transaction',
                array('checkProducts', 'reserveProducts', 'buy', 'confirm'),
                new Service\Transaction(
                    $this->fromShop,
                    $this->gateway,
                    $this->getLogger()
                )
            );
        }

        return $this->registry;
    }

    /**
     * Get verificator
     *
     * Direct access to this class is provided for testing and verification.
     * Use this class to verify the structs you pass to the Mosaic SDK. Mosaic
     * will do this itself, but it might be useful to also do this yourself.
     *
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
                         new Struct\Verificator\Change\InsertOrUpdate(),
                    'Mosaic\\SDK\\Struct\\Change\\FromShop\\Update' =>
                         new Struct\Verificator\Change\InsertOrUpdate(),
                    'Mosaic\\SDK\\Struct\\Change\\FromShop\\Delete' =>
                         new Struct\Verificator\Change(),
                    'Mosaic\\SDK\\Struct\\Change\\ToShop\\InsertOrUpdate' =>
                         new Struct\Verificator\Change\InsertOrUpdate(),
                    'Mosaic\\SDK\\Struct\\Change\\ToShop\\Delete' =>
                         new Struct\Verificator\Change\Delete(),
                    'Mosaic\\SDK\\Struct\\Change\\InterShop\\Update' =>
                         new Struct\Verificator\Change\InterShopUpdate(),
                    'Mosaic\\SDK\\Struct\\Change\\InterShop\\Delete' =>
                         new Struct\Verificator\Change\InterShopDelete(),
                    'Mosaic\\SDK\\Struct\\ShopConfiguration' =>
                         new Struct\Verificator\ShopConfiguration(),
                    'Mosaic\\SDK\\Struct\\Reservation' =>
                         new Struct\Verificator\Reservation(),
                    'Mosaic\\SDK\\Struct\\Message' =>
                         new Struct\Verificator\Message(),
                    'Mosaic\\SDK\\Struct\\Address' =>
                         new Struct\Verificator\Address(),
                )
            );
        }

        return $this->verificator;
    }

    /**
     * @private
     * @return Rpc\Marshaller\CallUnmarshaller
     */
    protected function getUnmarshaller()
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
    protected function getMarshaller()
    {
        if ($this->marshaller === null) {
            $this->marshaller = new Rpc\Marshaller\CallMarshaller\XmlCallMarshaller(
                new \Mosaic\Common\XmlHelper()
            );
        }

        return $this->marshaller;
    }

    /**
     * @private
     * @return Service\Shopping
     */
    protected function getShoppingService()
    {
        if ($this->shoppingService === null) {
            $this->shoppingService = new Service\Shopping(
                new ShopFactory\Http($this->gateway),
                $this->getChangeVisitor(),
                $this->getLogger()
            );
        }

        return $this->shoppingService;
    }

    /**
     * @private
     * @return Service\Verification
     */
    protected function getVerificationService()
    {
        if ($this->verificationService === null) {
            $this->verificationService = new Service\Verification(
                $this->getHttpClient('http://sn.mosaic/'),
                $this->gateway
            );
        }

        return $this->shoppingService;
    }

    /**
     * @private
     * @return Service\Syncer
     */
    protected function getSyncService()
    {
        if ($this->syncService === null) {
            $this->syncService = new Service\Syncer(
                $this->gateway,
                $this->gateway,
                $this->fromShop,
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
    protected function getProductHasher()
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
    protected function getRevisionProvider()
    {
        if ($this->revisionFromShop === null) {
            $this->revisionFromShop = new RevisionProvider\Time();
        }

        return $this->revisionFromShop;
    }

    /**
     * @private
     * @return ChangeVisitor
     */
    protected function getChangeVisitor()
    {
        if ($this->changeVisitor === null) {
            $this->changeVisitor = new ChangeVisitor\Message(
                $this->getVerificator()
            );
        }

        return $this->changeVisitor;
    }

    /**
     * @private
     * @return Logger
     */
    protected function getLogger()
    {
        if ($this->logger === null) {
            $this->logger = new Logger\Http(
                $this->getHttpClient('http://logger.mosaic/')
            );
        }

        return $this->logger;
    }

    /**
     * @private
     * @param string $server
     * @return Logger
     */
    protected function getHttpClient($server)
    {
        return new HttpClient\Stream($server);
    }
}
