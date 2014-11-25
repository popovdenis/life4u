<?php

namespace LifeSafe4U\BasicBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('LifeSafe4UBasicBundle:Default:index.html.twig');
    }
}
