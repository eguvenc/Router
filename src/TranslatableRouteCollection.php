<?php

namespace Obullo\Router;

use Obullo\Router\Exception\PathTranslationException;
use Laminas\I18n\Translator\TranslatorAwareInterface;
use Laminas\I18n\Translator\TranslatorAwareTrait;

/**
 * TranslatableRoute collection
 *
 * @copyright Obullo
 * @license   https://opensource.org/licenses/BSD-3-Clause
 */
class TranslatableRouteCollection extends RouteCollection implements TranslatorAwareInterface
{
    use TranslatorAwareTrait;

    /**
     * Add route
     *
     * @param RouteInterface $route object
     */
    public function add(RouteInterface $route)
    {
        $route->setPattern($this->pattern);
        $this->name = $route->getName();
        $route->convert();
        $this->routes[$this->name] = $route;
        
        $path = $route->getPath();
        $data = $this->translatePath($path);

        if ($data['match'] == true) {
            $translatedRoute = clone $route;
            $translatedRoute->setPath($data['path']);
            $translatedRoute->setName($data['path']);
            $this->add($translatedRoute);
        }
        return $this;
    }

    /**
     * Translate path segments
     *
     * @param  string $path route path
     * @return string new path
     * @return string $locale
     */
    public function translatePath($path, $locale = null)
    {
        $segments = explode('/', $path);
        $newPath = '';
        $match = false;
        foreach ($segments as $segment) {
            if (Self::isTranslatePattern($segment)) {
                $segment = $this->translator->translate($segment, $this->translatorTextDomain, $locale);
                $match = true;
            }
            if (Self::isTranslatePattern($segment)) {
                throw new PathTranslationException(
                    sprintf(
                        "No route translation found corresponding to item '%s'.",
                        $segment
                    )
                );
            }
            $newPath.= $segment.'/';
        }
        return ['path' => rtrim($newPath, '/'), 'match' => $match];
    }

    /**
     * Check route segment is translatable pattern
     *
     * @param  array $arr array
     * @return boolean
     */
    protected static function isTranslatePattern($segment)
    {
        $first = substr($segment, 0, 1);
        $last  = substr($segment, -1);

        if ($first == '{' && $last == '}') {
            return true;
        }
        return false;
    }
}
