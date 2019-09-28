<?php

namespace Obullo\Router;

/**
 * Translator interface.
 */
interface TranslatorInterface
{
    /**
     * Translate a message.
     *
     * @param  string $message
     * @param  string $textDomain
     * @param  string $locale
     * @return string
     */
    public function translate($message, $textDomain = 'default', $locale = null);
}
