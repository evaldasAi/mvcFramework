<?php

namespace App;

use App\Middleware\Cache;
use App\Middleware\Router;
use ProjectServiceContainer;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Relay\Relay;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Kernel
{
    private $booted = false;
    private $debug;
    private $environment;

    /** @var Container */
    private $container;

    public function __construct(string $env, bool $debug = true)
    {
        $this->environment = $env;
        $this->debug = $debug;
    }

    public function handle(RequestInterface $request): ResponseInterface
    {
        $this->boot();
        
//        $middlewares[] = new Cache();
        $middlewares[] = new Router();

        $relay = new Relay($middlewares);

        return $relay->handle($request);
    }

    public function boot()
    {
        if($this->booted) return;
        $containerDumpFile = $this->getProjectDir().'/var/cache/'.$this->environment.'/container.php';

        if(!$this->debug && file_exists($containerDumpFile)){
            require_once $containerDumpFile;
            $container = new ProjectServiceContainer();
        } else{
            $container = new ContainerBuilder();

            $container->setParameter('kernel.project_dir', $this->getProjectDir());
            $container->setParameter('kernel.environment', $this->environment);

            $loader = new YamlFileLoader($container, new FileLocator($this->getProjectDir().'/config'));
            
            try{
                $loader->load('services.yaml');
                $loader->load('services_'.$this->environment.'.yaml');
            }catch(\Exception $e){
            }

            $container->compile();

            @mkdir(dirname($containerDumpFile), 0777, true);
            file_put_contents($containerDumpFile, 
                (new PhpDumper($container))->dump(['array' => 'ProjectServiceContainer'])
            );
        }

        $this->container = $container;
        $this->booted = true;
    }

    function getProjectDir(){
        return \dirname(__DIR__);
    }

    function getContainer(){
        return $this->container;
    }
}