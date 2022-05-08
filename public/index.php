<?php

use App\Kernel;
use Nyholm\Psr7\Factory\Psr17Factory;

require __DIR__.'/../vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$psr17Factory = new Psr17Factory();

$creator = new \Nyholm\Psr7Server\ServerRequestCreator(
    $psr17Factory, // ServerRequestFactory
    $psr17Factory, // UriFactory
    $psr17Factory, // UploadedFileFactory
    $psr17Factory  // StreamFactory
);

$request = $creator->fromGlobals();

$kernel = new Kernel('dev',false);
$response = $kernel->handle($request);

echo $response->getBody(); exit;
