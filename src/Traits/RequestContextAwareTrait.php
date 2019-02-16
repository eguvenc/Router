<?php

namespace Obullo\Router\Traits;

use Obullo\Router\RequestContext;

/**
 * RequestContextAwareTrait
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
trait RequestContextAwareTrait
{
    protected $requestContext;
    
    /**
     * Set request context
     *
     * @param RequestContext $context object
     */
    public function setContext(RequestContext $context)
    {
        $this->requestContext = $context;
    }

    /**
     * Returns to request context
     *
     * @return object
     */
    public function getContext() : RequestContext
    {
        return $this->requestContext;
    }
}
