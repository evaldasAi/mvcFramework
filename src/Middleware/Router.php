<?php

namespace App\Middleware;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Router implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uri = $request->getUri()->getPath();
        
        $routes = [
            '/public/' => 'App\\Controller\\IndexController@run',
        ];
        
        if(isset($routes[$uri])) {
            list($name, $action) = explode('@', $routes[$uri]);
            $controller = new $name();

            return $controller->$action($request);
        };
        
        return new Response(404,[],'Not found!');
    }
}