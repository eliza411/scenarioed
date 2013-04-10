<?php

namespace ScenarioEd\ProjectBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ScenarioEd\ProjectBundle\Entity\Project;
use ScenarioEd\ProjectBundle\Entity\Feature;
use ScenarioEd\ProjectBundle\Form\FeatureType;

/**
 * Feature controller.
 *
 * @Route("/project/{project_id}/feature")
 */
class FeatureController extends BaseController
{
    /**
     * Finds and displays a Feature.
     *
     * @Route("/", name="project_feature_show")
     * @Template()
     */
    public function showAction($project_id)
    {
        $request = $this->getRequest();
        $file = $request->query->get('file');

        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository('ScenarioEdProjectBundle:Project')->find($project_id);
        $features = $this->loadFeatures($project->getRepositoryUri(), $file);
        $feature = $features[0];

        if (!$project) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        $deleteForm = $this->createDeleteForm($file);

        return array(
            'project' => $project,
            'feature' => $feature,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Feature entity.
     *
     * @Route("/new", name="project_feature_new")
     * @Template()
     */
    public function newAction($project_id)
    {
        $request = $this->getRequest();
        $file = $request->query->get('file');

        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository('ScenarioEdProjectBundle:Project')->find($project_id);

        if (!$project) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        $form = $this->createCreateForm($project_id, $file);

        return array(
            'form'   => $form->createView(),
            'project' => $project,
        );
    }

    /**
     * Creates a new Feature entity.
     *
     * @Route("/create", name="project_feature_create")
     * @Method("POST")
     * @Template("ScenarioEdProjectBundle:Feature:new.html.twig")
     */
    public function createAction(Request $request, $project_id)
    {
        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository('ScenarioEdProjectBundle:Project')->find($project_id);

        $form = $this->createCreateForm($project_id);
        $form->bind($request);

        $data = $form->getData();
        $entity  = new Feature($project->getRepositoryURI().DIRECTORY_SEPARATOR.'features/' . $data['file']);

        if ($form->isValid()) {
            $entity->create();
            $entity->setContents("Feature: New feature\n");

            return $this->redirect($this->generateUrl('project_show', array('id' => $project->getId())));
        }

        return array(
            'project' => $project,
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Feature entity.
     *
     * @Route("/edit", name="project_feature_edit")
     * @Template()
     */
    public function editAction($project_id)
    {
        $request = $this->getRequest();
        $file = $request->query->get('file');

        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository('ScenarioEdProjectBundle:Project')->find($project_id);

        if (!$project) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        $feature = new Feature($file);
        //Load the first FeatureNode for this feature in order to grab the title.
        //We should check it exists and throw an exception if not but I'm avoiding that
        //since we will probably not continue in this way due to problems accessing extensions.
        $featureNode = $this->loadFeatures($project->getRepositoryUri(), $file)[0];
        $feature->title = $featureNode->getTitle();

        $editForm = $this->createForm(new FeatureType(), $feature);
        $deleteForm = $this->createDeleteForm($file);

        return array(
            'project'     => $project,
            'feature'     => $feature,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Feature entity.
     *
     * @Route("/update", name="project_feature_update")
     * @Method("POST")
     * @Template("ScenarioEdProjectBundle:Feature:edit.html.twig")
     */
    public function updateAction(Request $request, $project_id)
    {
        $request = $this->getRequest();
        $file = $request->query->get('file');

        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository('ScenarioEdProjectBundle:Project')->find($project_id);

        if (!$project) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        $feature = new Feature($file);

        $deleteForm = $this->createDeleteForm($file);
        $editForm = $this->createForm(new FeatureType(), $feature);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            // Do update.
            $this->get('session')->getFlashBag()->add('message', 'The Feature was successfully updated.');
            return $this->redirect($this->generateUrl('project_feature_show', array('project_id' => $project_id, 'file' => $file)));
        }

        return array(
            'feature'      => $feature,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Feature entity.
     *
     * @Route("/delete", name="project_feature_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $project_id)
    {
        $request = $this->getRequest();
        $file = $request->query->get('file');

        $form = $this->createDeleteForm($file);
        $form->bind($request);

        $feature = new Feature($file);


        if ($form->isValid()) {
            $feature->delete();
        }

        return $this->redirect($this->generateUrl('project_feature_show', array('project_id' => $project_id, 'file' => $file)));
    }

    private function createDeleteForm($file)
    {
        return $this->createFormBuilder(array('file' => $file))
            ->add('file', 'hidden')
            ->getForm()
        ;
    }

    private function createCreateForm()
    {
        return $this->createFormBuilder()
            ->add('file', 'text')
            ->getForm()
        ;
    }
}
