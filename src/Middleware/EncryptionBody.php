<?php

namespace ZhiEq\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use ZhiEq\Contracts\MiddlewareExceptRoute;
use ZhiEq\Encrypt\AESEncrypt;

class EncryptionBody extends MiddlewareExceptRoute
{

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function subHandle($request, Closure $next)
    {
        /**
         * @var Response $response
         */
        $response = $next($request);
        if (!empty($response->getContent())) {
            $response->setContent(json_encode(['encryptionData' => AESEncrypt::quickEncrypt($response->getContent())]));
        }
        return $response;
    }
}
