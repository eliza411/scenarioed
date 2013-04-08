<?php

namespace ScenarioEd\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ScenarioEdUserBundle:Default:index.html.twig', array('name' => $name));
    }
}
