<?php

namespace Gigablah\Silex\View\EventListener;

use Gigablah\Silex\View\ViewFactory;
use Gigablah\Silex\View\ViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Renders view output.
 *
 * @author Chris Heng <bigblah@gmail.com>
 */
class RenderListener implements EventSubscriberInterface
{
    private $factory;

    public function __construct(ViewFactory $factory)
    {
        $this->factory = $factory;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        if ($event->getRequest()->getRequestFormat() !== 'html') {
            return;
        }

        $view = $event->getResponse()->getContent();

        if (!$view instanceof ViewInterface) {
            return;
        }

        $content = (string) $view;

        if ($this->factory->getExceptionBag()->count()) {
            $view['_error'] = $this->factory->getExceptionBag()->pop()->getMessage();
            $content = '<pre>'.json_encode($view->toArray() + $view->globals(), JSON_PRETTY_PRINT).'</pre>';
        }

        $event->getResponse()->setContent($content);
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => array('onKernelResponse', -64)
        );
    }
}
