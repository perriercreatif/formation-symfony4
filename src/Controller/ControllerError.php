<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ControllerError extends AbstractController {

    public function error()
    {
        return $this->render('bundles/TwigBundle/Exception/error404.html.twig');
    }
}