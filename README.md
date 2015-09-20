InjectorServiceProvider
======
[![Build Status](https://travis-ci.org/gonzalo123/injector.svg)](https://travis-ci.org/gonzalo123/injector)

Alternative way to define service providers in Silex


Let's say we've got this Math class
```php
namespace Foo

class Math
{
    public function sum($i, $j)
    {
        return $i+$j;
    }
}
```

And we want to use it within a Silex application

```php
include __DIR__ . "/../vendor/autoload.php";

use Silex\Application;
use Foo\Math;

$app            = new Application(['debug' => true]);

$app['math'] = function () {
    return new Math();
};

$app->get("/", function () use ($app) {
    return $app['math']->sum(1, 2);
});

$app->run();
```

We have one Service available in $app['math'], but, what's the type of the class? We need to inspect Math class to figure out what public functions are available.
Sometimes I'm a bit lazy to do that, and because of that I've develop this small service provider to allow us to use a different approach to define our service providers.

```php
include __DIR__ . "/../vendor/autoload.php";

use Silex\Application;
use Injector\InjectorServiceProvider;
use Foo\Math;

$app            = new Application(['debug' => true]);

$app->register(new InjectorServiceProvider([
    'Math' => 'math',
]));

$app['math'] = function () {
    return new Math();
};

$app->get("/", function (Math $math) {
    return $math->sum(1, 2);
});

$app->run();
```

And that's all. Our  InjectorServiceProvider allows us to define the class provided by the service provider, and its Silex/Pimple key name in the Dependency Injection Contailer