<?php

namespace nacholibre\DoctrineTranslatableFormBundle\Twig;

use Doctrine\Common\Annotations\AnnotationReader;

class CountryCodeExtension extends \Twig_Extension {
    public function __construct($countryCodeService) {
        $this->ccService = $countryCodeService;
    }

    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('nacholibre_get_locale', [$this, 'getLocale']),
        ];
    }

    public function getLocale($locale) {
        return $this->ccService->getCountryByIso($locale);
    }

    public function getName() {
        return 'nacholibre_get_locale';
    }
}
