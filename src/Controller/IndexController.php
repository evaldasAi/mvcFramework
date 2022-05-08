<?php

namespace App\Controller;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

class IndexController
{
    public function run(ServerRequestInterface $req): Response
    {
        return new Response(200, [], 'Index');
    }
}