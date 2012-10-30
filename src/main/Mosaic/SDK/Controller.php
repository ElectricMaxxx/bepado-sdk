<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK;

use Mosaic\Common\Rpc;

/**
 * Centra controller, which is addressed by web requests to the SDK web service 
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

    public function __construct(Rpc\ServiceRegistry $registry, Rpc\Marshaller\CallUnmarshaller $unmarshaller, Rpc\Marshaller\CallMarshaller $marshaller)
    {
        $this->registry = $registry;
        $this->unmarshaller = $unmarshaller;
        $this->marshaller = $marshaller;
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
            $this->registry->dispatch(
                $this->unmarshaller->unmarshal($xml)
            )
        );
    }
}
