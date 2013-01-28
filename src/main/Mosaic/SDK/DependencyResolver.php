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
 * SDK Dependency Resolver
 *
 * Resolves the dependencies of the SDK components.
 */
class DependencyResolver
{

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
     * Search service
     *
     * @var Service\Search
     */
    protected $searchService;

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
     * @var string
     */
    protected $socialNetworkHost = 'http://socialnetwork.mosaic.local';

    /**
     * @var string
     */
    protected $transactionHost = 'http://transaction.mosaic.local';

    /**
     * @var string
     */
    protected $searchHost = 'http://search.mosaic.local';

    /**
     * @param \Mosaic\SDK\Gateway $gateway
     * @param \Mosaic\SDK\ProductToShop $toShop
     * @param \Mosaic\SDK\ProductFromShop $fromShop
     */
    public function __construct(
        Gateway $gateway,
        ProductToShop $toShop,
        ProductFromShop $fromShop
    ) {
        $this->gateway = $gateway;
        $this->toShop = $toShop;
        $this->fromShop = $fromShop;

        if ($host = getenv('_SOCIALNETWORK_HOST')) {
            $this->socialNetworkHost = "http://{$host}";
        }
        if ($host = getenv('_TRANSACTION_HOST')) {
            $this->transactionHost = "http://{$host}";
        }
        if ($host = getenv('_SEARCH_HOST')) {
            $this->searchHost = "http://{$host}";
        }
    }

    /**
     * Get gateway
     *
     * Access to the gateway implementation
     *
     * @return Gateway
     */
    public function getGateway()
    {
        return $this->gateway;
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
     * @return \Mosaic\SDK\Struct\VerificatorDispatcher
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
                        new Struct\Verificator\Product(
                            $this->gateway->getCategories()
                        ),
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
     * @return Rpc\Marshaller\CallMarshaller
     */
    public function getMarshaller()
    {
        if ($this->marshaller === null) {
            $this->marshaller = new Rpc\Marshaller\CallMarshaller\XmlCallMarshaller(
                new \Mosaic\Common\XmlHelper()
            );
        }

        return $this->marshaller;
    }

    /**
     * @return Service\Shopping
     */
    public function getShoppingService()
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
     * @return Service\Verification
     */
    public function getVerificationService()
    {
        if ($this->verificationService === null) {
            $this->verificationService = new Service\Verification(
                $this->getHttpClient($this->socialNetworkHost),
                $this->gateway
            );
        }

        return $this->verificationService;
    }

    /**
     * @return Service\Search
     */
    public function getSearchService()
    {
        if ($this->searchService === null) {
            $this->searchService = new Service\Search(
                $this->getHttpClient($this->searchHost)
            );
        }

        return $this->searchService;
    }

    /**
     * @return Service\Syncer
     */
    public function getSyncService()
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
     * @return RevisionProvider
     */
    public function getRevisionProvider()
    {
        if ($this->revisionFromShop === null) {
            $this->revisionFromShop = new RevisionProvider\Time();
        }

        return $this->revisionFromShop;
    }

    /**
     * @return ChangeVisitor
     */
    public function getChangeVisitor()
    {
        if ($this->changeVisitor === null) {
            $this->changeVisitor = new ChangeVisitor\Message(
                $this->getVerificator()
            );
        }

        return $this->changeVisitor;
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        if ($this->logger === null) {
            $this->logger = new Logger\Http(
                $this->getHttpClient($this->transactionHost)
            );
        }

        return $this->logger;
    }

    /**
     * @param string $server
     * @return \Mosaic\SDK\HttpClient
     */
    public function getHttpClient($server)
    {
        return new HttpClient\Stream($server);
    }
}
