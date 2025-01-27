<?php

namespace Zbxin\Encrypt\GuzzleMiddleware;

use GuzzleHttp\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zbxin\Encrypt\AESEncrypt;

class DecryptionBodyGuzzleMiddleware
{
    protected $secretKey;

    public function __construct($secretKey)
    {
        $this->secretKey = $secretKey;
    }

    /**
     * @param callable $handler
     * @return \Closure
     */

    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            /**
             * @var Promise $promise
             */
            $promise = $handler($request, $options);
            return $promise->then(
                function (ResponseInterface $response) {
                    $content = $response->getBody()->getContents();
                    $encryptData = !empty($content) ? json_decode($content, true) : null;
                    $encryptData = isset($encryptData['encryptionData']) ? $encryptData['encryptionData'] : null;
                    if (!empty($encryptData)) {
                        $response = $response->withBody(\GuzzleHttp\Psr7\stream_for(AESEncrypt::decrypt($encryptData, $this->secretKey)));
                    }
                    return $response;
                }
            );
        };
    }
}
