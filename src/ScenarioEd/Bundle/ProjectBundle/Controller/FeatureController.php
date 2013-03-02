<?php

namespace ScenarioEd\Bundle\ProjectBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ScenarioEd\Bundle\ProjectBundle\Form\FeatureType;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

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

        return array(
            'project' => $project,
            'feature' => $feature,
        );
    }
}
