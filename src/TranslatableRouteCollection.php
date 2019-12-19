<?php

namespace Obullo\Router;

use Obullo\Router\TranslatorInterface;
use Obullo\Router\Exception\PathTranslationException;

/**
 * TranslatableRoute collection
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class TranslatableRouteCollection extends RouteCollection
{
    /**
    * Translator used for translatable segments.
    *
    * @var Translator
    */
    protected $translator;

    /**
     * Translator text domain to use.
     *
     * @var string
     */
    protected $translatorTextDomain = 'default';

    /**
     * Set translator object
     *
     * @param Obullo\Router\TranslatorInterface|object $translator translator
     * @param strin g                    $textDomain text domain
     */
    public function setTranslator($translator = null, $textDomain = null)
    {
        $this->translator = $translator;
        if ($textDomain !== null) {
            $this->setTranslatorTextDomain($textDomain);
        }
        return $this;
    }

    /**
     * Returns to translator object
     *
     * @return object
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * Set translator text domain
     *
     * @param string $textDomain text domain
     */
    public function setTranslatorTextDomain($textDomain = 'default')
    {
        $this->translatorTextDomain = $textDomain;
        return $this;
    }

    /**
     * Returns to string
     *
     * @return string
     */
    public function getTranslatorTextDomain() : string
    {
        return $this->translatorTextDomain;
    }

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
