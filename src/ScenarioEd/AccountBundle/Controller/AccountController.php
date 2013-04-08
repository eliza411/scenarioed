<?php 
namespace ScenarioEd\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use ScenarioEd\AccountBundle\Form\Type\RegistrationType;
use ScenarioEd\AccountBundle\Form\Model\Registration;

class AccountController extends Controller
{
    public function registerAction()
    {
        $form = $this->createForm(
            new RegistrationType(),
            new Registration()
        );

        return $this->render(
            'ScenarioEdAccountBundle:Account:register.html.twig',
            array('form' => $form->createView())
        );
    }

    public function createAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $form = $this->createForm(new RegistrationType(), new Registration());

        $form->bind($this->getRequest());

        if ($form->isValid()) {
            $registration = $form->getData();

            $em->persist($registration->getUser());
            $em->flush();
            // junky
            return $this->redirect($this->generateUrl(array('user' => $user->getId())));
        }

        return $this->render(
            'ScenarioEdAccountBundle:Account:register.html.twig',
            array('form' => $form->createView())
        );
    }
}

