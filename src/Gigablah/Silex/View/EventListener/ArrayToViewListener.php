<?php

namespace Gigablah\Silex\View\EventListener;

use Gigablah\Silex\View\ViewFactory;
use Gigablah\Silex\View\Template\TemplateResolverInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Converts arrays to views.
 *
 * @author Chris Heng <bigblah@gmail.com>
 */
class ArrayToViewListener
{
    private $factory;
    private $resolver;

    public function __construct(ViewFactory $factory, TemplateResolverInterface $resolver)
    {
        $this->factory = $factory;
        $this->resolver = $resolver;
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $result = $event->getControllerResult();

        if (!is_array($result)) {
            return;
        }

        $result = $this->factory->create($this->resolver->resolve($event->getRequest(), $result), $result);

        $event->setResponse(new Response($result));
    }
}
