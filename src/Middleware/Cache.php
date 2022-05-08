<?php


namespace App\Middleware;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Cache\Adapter\Apcu\ApcuCachePool;

class Cache implements MiddlewareInterface
{

    private $cache;
    private $ttl;

    public function __construct()
    {
        $this->cache = new ApcuCachePool();
        $this->ttl = 300;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uri = $request->getUri();
        $cacheKey = 'uri'.sha1($uri->getPath().'?'.$uri->getQuery());
        $cacheItem = $this->cache->getItem($cacheKey);

        if($cacheItem->isHit()){
            $request->getBody()->write($cacheItem->get());

            return $request;
        }


    }
}