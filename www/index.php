<?php

include __DIR__ . "/../vendor/autoload.php";

include __DIR__ . "/Foo/Gonzalo.php";

use Foo\Gonzalo;
use Injector\InjectorServiceProvider;
use Silex\Application;

$app            = new Application(['debug' => true]);
$app['gonzalo'] = function () {
    return new Gonzalo();
};

$app->register(new InjectorServiceProvider([
    'Foo\Gonzalo' => 'gonzalo',
]));

$app->get("/{name}", function (Gonzalo $g, $name) {
    return $g->hello($name);
});

$app->run();