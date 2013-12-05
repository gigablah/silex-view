<?php

namespace Gigablah\Silex\View\DataCollector;

use Gigablah\Silex\View\ViewFactory;
use Gigablah\Silex\View\Logger\ViewLoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * Collects view rendering data for the Symfony2 profiler.
 *
 * @author Chris Heng <bigblah@gmail.com>
 */
class ViewDataCollector extends DataCollector
{
    protected $factory;
    protected $logger;

    /**
     * Constructor.
     *
     * @param ViewFactory         $factory
     * @param ViewLoggerInterface $logger
     */
    public function __construct(ViewFactory $factory, ViewLoggerInterface $logger)
    {
        $this->factory = $factory;
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data['views'] = array();

        foreach ($this->logger->getViews() as $viewData) {
            $view = $viewData['view'];
            $time = $viewData['time'];
            $data = array();

            foreach ($view as $key => $value) {
                $data[$key] = $this->varToString($value);
            }

            $template = null !== $view->getTemplate()
                ? $view->getTemplate()
                : get_class($view);

            $engine = null !== $view->getEngine()
                ? basename(str_replace('\\', '/', get_class($view->getEngine())))
                : null;

            $datum = array(
                'template' => $template,
                'engine' => $engine,
                'data' => $data,
                'time' => $time
            );

            $this->data['views'][] = $datum;
        }

        $this->data['views'] = array_reverse($this->data['views']);
        $this->data['globals'] = $this->factory->getSharedBag()->all();
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'view';
    }

    /**
     * Returns the total time spend on rendering.
     *
     * @return float
     */
    public function getTime()
    {
        $time = 0;
        foreach ($this->data['views'] as $view) {
            $time += (float) $view['time'];
        }

        return $time;
    }
}
