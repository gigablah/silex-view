<?php

namespace Gigablah\Silex\View\Template;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface for resolving templates.
 *
 * @author Chris Heng <bigblah@gmail.com>
 */
interface TemplateResolverInterface
{
    /**
     * Returns the template path.
     *
     * @param Request $request          The request entity
     * @param mixed   $controllerResult The controller output
     *
     * @return string The template path
     */
    public function resolve(Request $request, $controllerResult = null);
}
