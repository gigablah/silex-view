<?php

namespace Gigablah\Silex\View;

use Gigablah\Silex\View\Engine\DelegatingEngine;
use Gigablah\Silex\View\Engine\StringEngine;
use Gigablah\Silex\View\Engine\PhpEngine;
use Gigablah\Silex\View\Engine\AuraEngine;
use Gigablah\Silex\View\Engine\MustacheEngine;
use Gigablah\Silex\View\Engine\SmartyEngine;
use Gigablah\Silex\View\Engine\TwigEngine;
use Gigablah\Silex\View\Engine\LazyEngineResolver;
use Gigablah\Silex\View\Template\TemplateResolver;
use Gigablah\Silex\View\Logger\ViewLogger;
use Gigablah\Silex\View\EventListener\ViewListener;
use Gigablah\Silex\View\EventListener\RenderListener;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * ViewServiceProvider registers the view factory for wrapping responses with views.
 *
 * @author Chris Heng <bigblah@gmail.com>
 */
class ViewServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['view.globals'] = array();

        $app['view.engines'] = array(
            'mustache' => 'view.engine.mustache',
            'ms' => 'view.engine.mustache',
            'tpl' => 'view.engine.smarty',
            'twig' => 'view.engine.twig',
            'php' => 'view.engine.php',
            'html' => 'view.engine.string'
        );

        $app['view.default_engine'] = 'html';

        $app['view.required_formats'] = array('html');

        $app['view'] = $app->share(function ($app) {
            $factory = $app['debug'] ? $app['view.factory.debug'] : $app['view.factory'];
            $factory->getSharedBag()->add($app['view.globals']);

            return $factory;
        });

        $app['view.factory'] = $app->share(function ($app) {
            return new ViewFactory($app['view.engine']);
        });

        $app['view.factory.debug'] = $app->share(function ($app) {
            return new LoggableViewFactory($app['view.engine'], $app['view.logger']);
        });

        $app['view.engine'] = $app->share(function ($app) {
            return new DelegatingEngine($app['view.engine_resolver']);
        });

        $app['view.engine.string'] = $app->share(function ($app) {
            return new StringEngine();
        });

        $app['view.engine.php'] = $app->share(function ($app) {
            return new PhpEngine();
        });

        $app['view.engine.aura'] = $app->share(function ($app) {
            return new AuraEngine($app['aura.template']);
        });

        $app['view.engine.mustache'] = $app->share(function ($app) {
            return new MustacheEngine($app['mustache']);
        });

        $app['view.engine.smarty'] = $app->share(function ($app) {
            return new SmartyEngine($app['smarty']);
        });

        $app['view.engine.twig'] = $app->share(function ($app) {
            return new TwigEngine($app['twig']);
        });

        $app['view.engine_resolver'] = $app->share(function ($app) {
            return new LazyEngineResolver($app, $app['view.engines'], $app['view.default_engine']);
        });

        $app['view.template_resolver'] = $app->share(function ($app) {
            return new TemplateResolver($app['view.default_engine']);
        });

        $app['view.logger'] = $app->share(function ($app) {
            $stopwatch = isset($app['debug.stopwatch']) ? $app['debug.stopwatch'] : null;

            return new ViewLogger($app['logger'], $stopwatch);
        });
    }

    public function boot(Application $app)
    {
        $app['dispatcher']->addSubscriber(new ViewListener($app['view'], $app['view.template_resolver'], $app['view.required_formats']));
        $app['dispatcher']->addSubscriber(new RenderListener($app['view']));
    }
}
