<?php

namespace ScenarioEd\HelpBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class HelpController extends Controller

{
    /**
     * @Route("/help/{name}")
     * @Template()
     */
    public function indexAction($name)
    {
        return array('name' => $name);
    }

    /**
     * @Route("/help", name="help")
     * @Template()
    */
    public function helpAction()
    {
        return array("help" => "HELP!");
    }

}
