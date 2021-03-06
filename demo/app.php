<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app['debug'] = true;

$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.handler' => new Monolog\Handler\SyslogHandler('silex-view')
));

$app['mustache'] = $app->share(function ($app) {
    return new \Mustache_Engine(array(
        'loader' => new \Mustache_Loader_FilesystemLoader(__DIR__)
    ));
});

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__
));

/**
 * This shows how to register the service provider.
 *
 * We can initialize the global context using view.globals.
 *
 * view.default_engine determines the file extension that
 * will be appended to an autogenerated template name.
 *
 * view.listener_priority configures the order in which the
 * ArrayToViewListener will fire during the controller view event.
 */
$app->register(new Gigablah\Silex\View\ViewServiceProvider(), array(
    'view.globals' => array(
        'title' => 'silex-view'
    ),
    'view.default_engine' => 'twig',
    'view.listener_priority' => -20
));

/**
 * We cannot initialize the "url" parameter using view.globals,
 * since that will prematurely instantiate url_generator.
 *
 * Therefore, we use share() inside a middleware instead.
 */
$app->before(function ($request) use ($app) {
    $app['view']->share(array(
        'url' => $app['url_generator']->generate('home')
    ));
});

/**
 * Calling render() will output a string.
 *
 * This way, the controller output will be handled by the default
 * StringToResponseListener supplied by Silex.
 */
$app->get('/', function () use ($app) {
    return $app['view']->create('home.twig', array(
        'url1' => $app['url_generator']->generate('example1'),
        'url2' => $app['url_generator']->generate('example2'),
        'url3' => $app['url_generator']->generate('example3'),
        'url4' => $app['url_generator']->generate('example4'),
        'url5' => $app['url_generator']->generate('example5'),
        'url6' => $app['url_generator']->generate('error')
    ))->render();
})->bind('home');

/**
 * Wrapping the view with a Response will cast it to a string.
 *
 * Casting to string is equivalent to calling the render() method.
 */
$app->get('/1', function () use ($app) {
    $view = $app['view']->create('example1.twig', array(
        'foo' => 'foo'
    ));
    return new Symfony\Component\HttpFoundation\Response($view);
})->bind('example1');

/**
 * Returning a view object is also possible.
 *
 * StringToResponseListener will cast the view to a string.
 */
$app->get('/2', function () use ($app) {
    return $app['view']->create('example2.twig', array(
        'foo' => 'bar'
    ));
})->bind('example2');

/**
 * We can also directly return an array.
 *
 * The TemplateResolver will autogenerate the template name based on
 * the route, which is defined here as "example3". Since our default
 * engine is twig, the final template name is "example3.twig".
 */
$app->get('/3', function () use ($app) {
    return array(
        'foo' => 'baz'
    );
})->bind('example3');

/**
 * Instead of autogenerating the template name, we can specify it in
 * the controller output array using _template.
 */
$app->get('/4', function () use ($app) {
    return array(
        'foo' => 'moo',
        '_template' => 'example4.html'
    );
})->bind('example4');

/**
 * We can also configure the template in the request attributes.
 */
$app->get('/5', function (Symfony\Component\HttpFoundation\Request $request) use ($app) {
    $request->attributes->set('_template', 'example5.php');
    return array(
        'foo' => 'boo'
    );
})->bind('example5');

/**
 * Exceptions can be handled as shown below.
 */
$app->get('/6', function () use ($app) {
    throw new \Exception('Hello this is error');
})->bind('error');

$app->error(function (\Exception $exception, $code) {
    return new Symfony\Component\HttpFoundation\Response($exception->getMessage(), $code);
});

/**
 * Finally, another middleware intercepts the response and wraps it
 * with a layout, which is simply another view.
 */
$app->after(function ($request, $response) use ($app) {
    $footer = $app['view']->create('footer.mustache', array('date' => date(DATE_RFC2822)));
    $layout = $app['view']->create('layout.twig')->nest($footer, 'footer');
    $response->setContent($layout->with(array(
        'content' => $response->getContent()
    )));
});

$app->run();
