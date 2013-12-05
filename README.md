ViewServiceProvider
===================

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
    'view.globals' => array(
        'foo' => 'bar'
    ),
    'view.default_engine' => 'mustache'
));
```

The provider registers two listeners. The `ViewListener` intercepts the output from your controllers and wraps it with a `View` object. The output now forms your view context. The `RenderListener` listens in to the response event and renders your views into a string with the appropriate engine.

Creating Views
--------------

Normally you do not need to instantiate any view entities on your own; the listener will convert your controller output. If you wish to do it manually, the syntax is as follows:

```php
$app['view']->create($template = '/path/to/template', $context = array('foo' => 'bar'));
```

Resolving Templates
-------------------

How does the listener know which template to use? By default it reads the `_route` attribute from the request entity in lowercase. Some examples:

```php
$app->get('/foobar', function () {}); // get_foobar
$app->get('/', function () {}); // get_
$app->match('/', function () {}); // _
```

Since you probably want more descriptive template names, you can use named routes:

```php
$app->match('/', function () {})->bind('home'); // home
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
* Raw PHP
* Token replacement using strtr()

There is a special `DelegatingEngine` which acts as a registry for multiple different engines, selecting the appropriate one based on the template file extension.

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

Composite Views
---------------

Views can be nested inside another:

```php
$view->nest($app['view']->create('foobar.html'), 'section');
```

This is simply alternate syntax for:

```php
$view['section'] = $app['view']->create('foobar.html');
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

License
-------

Released under the MIT license. See the LICENSE file for details.

[1]: http://silex.sensiolabs.org
[2]: http://getcomposer.org
[3]: http://mustache.github.io
[4]: http://www.smarty.net
[5]: http://twig.sensiolabs.org
[6]: http://github.com/auraphp/Aura.View
