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
 * Central controller, which is addressed by web requests to the SDK web service
 * endpoint.
 *
 * @version $Revision$
 * @api
 */
class Controller
{
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

    public function __construct(
        Rpc\ServiceRegistry $registry,
        Rpc\Marshaller\CallUnmarshaller $unmarshaller,
        Rpc\Marshaller\CallMarshaller $marshaller,
        Service\Shopping $shoppingService
    ) {
        $this->registry = $registry;
        $this->unmarshaller = $unmarshaller;
        $this->marshaller = $marshaller;
        $this->shoppingService = $shoppingService;
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
        return $this->marshaller->marshal(
            new RpcCall(
                array(
                    'service' => 'null',
                    'command' => 'return',
                    'arguments' => array($this->registry->dispatch(
                        $this->unmarshaller->unmarshal($xml)
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
     * @param Struct\Product[] $products
     * @return mixed
     */
    public function checkProducts(Struct\Order $order)
    {
        $order->verify();
        $this->shoppingService->checkProducts($order);
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
     * @param Struct\Product[] $products
     * @return mixed
     */
    public function reserveProducts(array $products)
    {
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
}
