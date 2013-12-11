<?php

namespace Gigablah\Silex\View\Template;

use Symfony\Component\HttpFoundation\Request;

/**
 * Resolves templates by request attributes or the controller output.
 *
 * @author Chris Heng <bigblah@gmail.com>
 */
class TemplateResolver implements TemplateResolverInterface
{
    protected $type;

    /**
     * Constructor.
     *
     * @param string $type Default template type to use
     */
    public function __construct($type = null)
    {
        $this->type = $type;
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(Request $request, $controllerResult = null)
    {
        $template = $request->attributes->get('_template');

        if (!$template && is_array($controllerResult) && isset($controllerResult['_template'])) {
            $template = $controllerResult['_template'];
        }

        if (!$template) {
            $template = strtolower($request->attributes->get('_route'));
        }

        return $template.(!pathinfo($template, PATHINFO_EXTENSION) && $this->type ? '.'.$this->type : '');
    }
}
