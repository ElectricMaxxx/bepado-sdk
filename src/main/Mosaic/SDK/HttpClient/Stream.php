<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\HttpClient;

use Mosaic\SDK\HttpClient;

/**
 * HTTP client implementation
 *
 * The constructor of this class expects the remote REST server as argument.
 * This includes host/ip, port and protocol.
 *
 * @version $Revision$
 */
class Stream extends HttpClient
{
    /**
     * Optional default headers for each request.
     *
     * @var string
     */
    private $header = '';

    /**
     * The remote REST server location.
     *
     * @var string
     */
    private $server;

    /**
     * Constructs a new REST client instance for the given <b>$server</b>.
     *
     * @param string $server Remote server location. Must include the used protocol.
     */
    public function __construct($server)
    {
        $url = parse_url(rtrim($server, '/'));
        $url += array(
            'scheme' => 'http',
            'host'   => null,
            'port'   => null,
            'user'   => null,
            'pass'   => null,
            'path'   => null,
        );

        if ($url['user'] || $url['pass']) {
            $this->header = 'Authorization: Basic ' .
                base64_encode("{$url['user']}:{$url['pass']}") . "\r\n";
        }

        $this->server = $url['scheme'] . '://' . $url['host'];
        if ($url['port']) {
            $this->server .= ':' . $url['port'];
        }
        $this->server .= $url['path'];
    }

    /**
     * Execute a HTTP request to the remote server
     *
     * @param string $method
     * @param string $path
     * @param mixed $body
     * @param array $headers
     * @return Reponse
     */
    public function request($method, $path, $body = null, array $headers = array())
    {
        $httpFilePointer = @fopen(
            $this->server . $path, 'r', false,
            stream_context_create(
                array(
                    'http' => array(
                        'method'        => $method,
                        'content'       => $body,
                        'ignore_errors' => true,
                        'header'        => implode("\r\n", $headers),
                    ),
                )
            )
        );

        if ($httpFilePointer === false) {
            throw new \RuntimeException(
                "Could not connect to server {$this->server}: " . error_get_last()
            );
        }

        $response = new HttpClient\Response();
        while (!feof($httpFilePointer)) {
            $response->body .= fgets($httpFilePointer);
        }

        $metaData   = stream_get_meta_data($httpFilePointer);
        $rawHeaders = isset($metaData['wrapper_data']['headers']) ? $metaData['wrapper_data']['headers'] : $metaData['wrapper_data'];

        foreach ($rawHeaders as $lineContent) {
            if (preg_match('(^HTTP/(?P<version>\d+\.\d+)\s+(?P<status>\d+))S', $lineContent, $match)) {
                $response->status = (int) $match['status'];
            } else {
                list($key, $value) = explode(':', $lineContent, 2);
                $response->headers[strtolower($key)] = ltrim($value);
            }
        }

        return $response;
    }
}
