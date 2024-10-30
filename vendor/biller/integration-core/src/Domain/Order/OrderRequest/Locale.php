<?php

namespace Biller\Domain\Order\OrderRequest;

use Biller\Domain\Exceptions\InvalidLocale;

/**
 * Class Locale
 *
 * @package Biller\Domain\Order
 */
class Locale
{
    private static $availableLocales = ['en', 'nl', 'da', 'fr', 'de', 'en_GB', 'nl_BE', 'fr_BE'];
    /**
     * @var string
     */
    private $locale;

    /**
     * @param string $locale
     */
    private function __construct($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @param string $code
     * @return Locale
     * @throws InvalidLocale
     */
    public static function fromCode($code)
    {
        if(!in_array($code, self::$availableLocales)) {
            throw new InvalidLocale();
        }
        return new self($code);
    }

    /**
     * @return Locale
     *
     * @throws InvalidLocale
     */
    public static function getDefault()
    {
        return self::fromCode('en');
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->locale;
    }
}