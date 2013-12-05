<?php

namespace Gigablah\Silex\View\EventListener;

use Gigablah\Silex\View\ViewFactory;
use Gigablah\Silex\View\ViewInterface;
use Gigablah\Silex\View\Template\TemplateResolverInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Wraps views for controller responses.
 *
 * @author Chris Heng <bigblah@gmail.com>
 */
class ViewListener implements EventSubscriberInterface
{
    private $factory;
    private $resolver;
    private $formats;

    public function __construct(ViewFactory $factory, TemplateResolverInterface $resolver, array $formats = array())
    {
        $this->factory = $factory;
        $this->resolver = $resolver;
        $this->formats = $formats;
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        if (!in_array($event->getRequest()->getRequestFormat(), $this->formats)) {
            return;
        }

        $result = $event->getControllerResult();

        if ($result instanceof Response) {
            return;
        }

        if (!$result instanceof ViewInterface) {
            $result = $this->factory->create($this->resolver->resolve($event->getRequest(), $result), $result);
        }

        $event->setControllerResult($result);
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::VIEW => array('onKernelView', -16)
        );
    }
}
