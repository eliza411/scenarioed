<?php

namespace ScenarioEd\Bundle\ProjectBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ScenarioEd\Bundle\ProjectBundle\Entity\Project;
use ScenarioEd\Bundle\ProjectBundle\Form\ProjectType;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Behat\Behat\DependencyInjection\BehatExtension;
use Behat\Behat\DependencyInjection\Configuration\Loader;

/**
 * Project controller.
 *
 * @Route("/project")
 */
class ProjectController extends Controller
{
    protected $container;
    private $basePath;

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
        $gherkin = $this->createContainer($entity->getRepositoryUri())->get('gherkin');

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'features'    => $gherkin->load(''),
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

            return $this->redirect($this->generateUrl('project_edit', array('id' => $id)));
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

    protected function createContainer($path)
    {
        $container = new ContainerBuilder();
        $this->loadCoreExtension($container, $this->loadConfiguration($container, $path));
        $container->compile();

        return $container;
    }
    
    protected function loadCoreExtension(ContainerBuilder $container, array $configs)
    {
        if (null === $this->basePath) {
            throw new \RuntimeException(
                'Suite basepath is not set. Seems you have forgot to load configuration first.'
            );
        }

        $extension = new BehatExtension($this->basePath);
        $extension->load($configs, $container);
        $container->addObjectResource($extension);
    }

    protected function loadConfiguration(ContainerBuilder $container, $path)
    {
        // locate paths
        $this->basePath = $path;
        $configPath = $this->getConfigurationFilePath($path);
 
        // read configuration
        $loader  = new Loader($configPath);
        return $loader->loadConfiguration('default');
    }

    protected function getConfigurationFilePath($path)
    {
        foreach (array_filter(array(
            $path.DIRECTORY_SEPARATOR.'behat.yml',
            $path.DIRECTORY_SEPARATOR.'behat.yml.dist',
            $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'behat.yml',
            $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'behat.yml.dist',
        ), 'is_file') as $file) {
            return $file;
        }
    }
    
}
