<?php

namespace nacholibre\DoctrineTranslatableFormBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {
    /**
    * @Route("/generate_slug/{name}", name="doctrine_translatable_forms.generate_slug")
    */
    public function generateSlugAction($name) {
    }
}
