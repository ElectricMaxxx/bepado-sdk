<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK;

/**
 * HTTP client implementation
 *
 * @version $Revision$
 */
abstract class HttpClient
{
    /**
     * Execute a HTTP request to the remote server
     *
     * @param string $method
     * @param string $path
     * @param mixed $body
     * @param array $headers
     * @return HttpClient\Reponse
     */
    abstract public function request($method, $path, $body = null, array $headers = array());
}
