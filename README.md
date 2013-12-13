ViewServiceProvider
===================

[![Build Status](https://travis-ci.org/gigablah/silex-view.png?branch=master)](https://travis-ci.org/gigablah/silex-view) [![Coverage Status](https://coveralls.io/repos/gigablah/silex-view/badge.png)](https://coveralls.io/r/gigablah/silex-view)

The ViewServiceProvider gives engine-agnostic templating capabilities to your [Silex][1] application.

Installation
------------

Use [Composer][2] to install the gigablah/silex-view library by adding it to your `composer.json`. You'll also need a rendering engine, such as [Mustache][3].

```json
{
    "require": {
        "silex/silex": "~1.0",
        "mustache/mustache": "~2.4",
        "gigablah/silex-view": "~0.0.1"
    }
}
```

Usage
-----

Just register the service provider and optionally pass in some defaults.

```php
$app->register(new Gigablah\Silex\View\ViewServiceProvider(), array(
    'view.globals' => array('foo' => 'bar'),
    'view.default_engine' => 'mustache'
));
```

The provider registers the `ArrayToViewListener` which intercepts the output from your controllers and wraps it with a `View` object. For it to work, you have to return an array of data from your controller function.

Views
-----

Normally you do not need to instantiate any view entities on your own; the listener will convert your controller output. If you wish to do it manually, the syntax is as follows:

```php
$view = $app['view']->create($template = '/path/to/template', $context = array('foo' => 'bar'));
```

Views can be rendered by calling the `render()` function, or casting to string:

```php
$output = $view->render();
$output = (string) $view;
```

Again, you should not need to render your views manually since they will be handled by the `Response` object.

View Context
------------

The view entity is simply an instance of `ArrayObject`, so you can use regular array notation to set the context, along with convenience functions like `with()`:

```php
$view['foo'] = 'bar';
$view->with(array('foo' => 'bar'));
```

To insert into the global context, use `share()`:

```php
$view->share(array('foo' => 'bar'));
```

You can initialize the global context by overriding `view.globals`.

Resolving Templates
-------------------

How does the listener know which template to use? By default it reads the `_route` attribute from the request entity in lowercase, and appends the extension based on the value of `view.default_engine`. Some examples:

```php
$app->get('/foobar', function () {}); // get_foobar.mustache
$app->get('/', function () {}); // get_.mustache
$app->match('/', function () {}); // _.mustache
```

Since you probably want more descriptive template names, you can use named routes:

```php
$app->match('/', function () {})->bind('home'); // home.mustache
```

You can also set the `_template` attribute in the request, or as part of the controller output:

```php
$app->get('/foo', function (Symfony\Component\HttpFoundation\Request $request) {
    $request->attributes->set('_template', 'foo.html');
});

$app->get('/bar', function () {
    return array('_template' => 'bar.html');
});
```

If you need custom logic for generating template paths, you can create your own class that implements `TemplateResolverInterface` and override `view.template_resolver`.

Engines
-------

This library does not handle any actual view rendering; that task is delegated to the templating library of your choice. Currently adapters are provided for:

* [Mustache][3]
* [Smarty][4]
* [Twig][5]
* [Aura.View][6]
* [Plates][7]
* Raw PHP
* Token replacement using strtr()

There is a special `DelegatingEngine` which acts as a registry for multiple different engines, selecting the appropriate one based on the template file extension. Since Aura.View, Plates and Raw PHP all use the same default file extension (.php), you will need to manually configure the extension mapping as follows:

```php
$app->register(new Gigablah\Silex\View\ViewServiceProvider(), array(
    'view.default_engine' => 'php',
    'view.engines' => array(
        'php' => 'view.engine.plates'
    )
));
```

Composite Views
---------------

Views can be nested inside another:

```php
$view->nest($app['view']->create('foobar.html'), 'section');
```

For a single view, it is equivalent to:

```php
$view['section'] = $app['view']->create('foobar.html');
```

However, the difference lies in nesting multiple views in the same location. Doing this will place the child views adjacent to each other rather than overwriting:

```php
$view->nest($app['view']->create('foobar.html'), 'section');
$view->nest($app['view']->create('foobar.html'), 'section'); // foobar.html is now repeated twice
```

What's more, you can mix and match different engines:

```php
$mustacheView = $app['view']->create('foo.mustache');
$smartyView = $app['view']->create('bar.tpl')->nest($mustacheView, 'section');
```

Nested views will inherit the context of their parent views.

Exception Handling
------------------

All rendering exceptions are captured and stored in a shared `ExceptionBag`.

To access the last thrown exception, or return all of them:

```php
$exception = $app['view']->getExceptionBag()->pop();
$exceptions = $app['view']->getExceptionBag()->all();
```

More Examples
-------------

You can view a code sample of various usage scenarios in the [demo application][8].

License
-------

Released under the MIT license. See the LICENSE file for details.

[1]: http://silex.sensiolabs.org
[2]: http://getcomposer.org
[3]: http://mustache.github.io
[4]: http://www.smarty.net
[5]: http://twig.sensiolabs.org
[6]: http://github.com/auraphp/Aura.View
[7]: http://platesphp.com
[8]: http://github.com/gigablah/silex-view/blob/master/demo/app.php
