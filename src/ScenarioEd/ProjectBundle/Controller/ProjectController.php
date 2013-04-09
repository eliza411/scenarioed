<?php

namespace ScenarioEd\ProjectBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ScenarioEd\ProjectBundle\Entity\Project;
use ScenarioEd\ProjectBundle\Form\ProjectType;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Dumper;


/**
 * Project controller.
 *
 * @Route("/project")
 */
class ProjectController extends BaseController
{
    /**
     * @Route("/welcome", name="welcome")
     * @Template()
    */
    public function welcomeAction()
    {
        return array("welcome" => "Welcome");
    }

    /**
     * Lists all Project entities.
     *
     * @Route("/", name="project")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('ScenarioEdProjectBundle:Project')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a Project entity.
     *
     * @Route("/{id}/show", name="project_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ScenarioEdProjectBundle:Project')->find($id);
        $features = $this->loadFeatures($entity->getRepositoryUri(), '');

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'features'    => $features,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Project entity.
     *
     * @Route("/new", name="project_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Project();
        $form   = $this->createForm(new ProjectType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Project entity.
     *
     * @Route("/create", name="project_create")
     * @Method("POST")
     * @Template("ScenarioEdProjectBundle:Project:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Project();
        $form = $this->createForm(new ProjectType(), $entity);
        $form->bind($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

       // the base dir should probably be configurable in yml somewhere
        $base_dir = "/tmp/";
        $user_id = "user-" . $this->getUser()->getId();
        $project_id = array('id' => $entity->getId());
        $fs_path = $base_dir . $user_id . "/project-" . $project_id['id'];
        $config_file = 'behat.local.yml';
        $behat_config = $fs_path . "/" .  $config_file;
        


        //write the file
        $fs = new Filesystem();
        $fs->mkdir($fs_path);
        $fs->touch($behat_config);

        $yaml = Yaml::parse($behat_config);
        $behatinfo = array(
            'foo' => 'bar',
            'bar' => array('foo' => 'bar', 'bar' => 'baz'),
         );
        $dumper = new Dumper();
        $yaml = $dumper->dump($behatinfo,2);
        file_put_contents($behat_config, $yaml);

        
        $this->get('session')->getFlashBag()->add('message', 'Added file '. $behat_config);
        return $this->redirect($this->generateUrl('project_show', array('id' => $entity->getId())));
            
        }
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Project entity.
     *
     * @Route("/{id}/edit", name="project_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ScenarioEdProjectBundle:Project')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        $editForm = $this->createForm(new ProjectType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Project entity.
     *
     * @Route("/{id}/update", name="project_update")
     * @Method("POST")
     * @Template("ScenarioEdProjectBundle:Project:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ScenarioEdProjectBundle:Project')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ProjectType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

         $this->get('session')->getFlashBag()->add('message', 'Your changes have been saved.');
         return $this->redirect($this->generateUrl('project_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Project entity.
     *
     * @Route("/{id}/delete", name="project_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ScenarioEdProjectBundle:Project')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Project entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('project'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
