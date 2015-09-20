<?php

use Foo\Gonzalo;
use Injector\InjectorServiceProvider;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

include __DIR__ . "/Foo/Gonzalo.php";

class SilexApplicationExampleTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleInyection()
    {
        $request = Request::create('/Gonzalo', 'GET');

        $app = new Application();

        $app['gonzalo'] = function () {
            return new Gonzalo();
        };

        $app->register(new InjectorServiceProvider([
            'Foo\Gonzalo' => 'gonzalo',
        ]));

        $app->get("/{name}", function (Gonzalo $g, $name) {
            return $g->hello($name);
        });

        $actual = $app->handle($request)->getContent();

        $this->assertEquals("Hi Gonzalo", $actual);
    }


    public function testInyectionAlternativeConfiguration()
    {
        $request = Request::create('/Ayuso', 'GET');

        $app = new Application(['debug' => true]);

        $app['gonzalo'] = function () {
            return new Gonzalo();
        };

        $injector = new InjectorServiceProvider();
        $injector->appendInjectables('Foo\Gonzalo', 'gonzalo');

        $app->register($injector);

        $app->get("/{name}", function (Gonzalo $g, $name) {
            return $g->hello($name);
        });

        $actual = $app->handle($request)->getContent();

        $this->assertEquals("Hi Ayuso", $actual);
    }

    public function testNonExistentClass()
    {
        $request = Request::create('/gonzalo', 'GET');

        $app = new Application();

        $app->register(new InjectorServiceProvider([
        ]));

        $app->get("/{name}", function (Gonzalo $g, $name) {
            return $g->hello($name);
        });

        $response = $app->handle($request);

        $this->assertEquals(500, $response->getStatusCode());
    }
}