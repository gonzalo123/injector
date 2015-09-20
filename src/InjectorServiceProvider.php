<?php

namespace Injector;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class InjectorServiceProvider implements ServiceProviderInterface
{
    private $injectables;

    public function __construct($injectables = [])
    {
        $this->injectables = $injectables;
    }

    public function appendInjectables($providedClass, $key)
    {
        $this->injectables[$providedClass] = $key;
    }

    public function register(Application $app)
    {
        $app->on(KernelEvents::CONTROLLER, function (FilterControllerEvent $event) use ($app) {
            $reflectionFunction = new \ReflectionFunction($event->getController());
            $parameters         = $reflectionFunction->getParameters();
            foreach ($parameters as $param) {
                $class = $param->getClass();
                if ($class && array_key_exists($class->name, $this->injectables)) {
                    $event->getRequest()->attributes->set($param->name, $app[$this->injectables[$class->name]]);
                }
            }
        });
    }

    public function boot(Application $app)
    {
    }
}