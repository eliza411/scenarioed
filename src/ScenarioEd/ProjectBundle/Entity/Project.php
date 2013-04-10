<?php

namespace ScenarioEd\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Events;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Dumper;



/**
 * Project
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="ScenarioEd\ProjectBundle\Entity\ProjectRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Project
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="repository_uri", type="string", length=255)
     */
    // TODO: This needs validation
    private $repository_uri;


    /**
     * @var string
     *
     * @ORM\Column(name="project_name", type="string", length=255)
     */
    private $project_name;

    /**
     * @var string
     *
     * @ORM\Column(name="project_description", type="text")
     */
    private $project_description;

    /**
     * @var string
     *
     * @ORM\Column(name="base_url", type="string", length=255)
     */
    private $base_url;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set repository_uri
     *
     * @param string $repositoryUri
     * @return Project
     */
    public function setRepositoryUri($repositoryUri)
    {

        $this->repository_uri = $repositoryUri;
    
        return $this;
    }

    /**
     * Get repository_uri
     *
     * @return string 
     */
    public function getRepositoryUri()
    {
        return $this->repository_uri;
    }

    /**
     * Set project_name
     *
     * @param string $projectName
     * @return Project
     */
    public function setProjectName($projectName)
    {
        $this->project_name = $projectName;

        return $this;
    }


    /**
     * Get project_name
     *
     * @return string 
     */
    public function getProjectName()
    {
        return $this->project_name;
    }


    /**
     * Set project_description
     *
     * @param string $projectDescription
     * @return Project
     */
    public function setProjectDescription($projectDescription)
    {
        $this->project_description = $projectDescription;

        return $this;
    }

    /**
     * Get project_description
     *
     * @return string 
     */
    public function getProjectDescription()
    {
        return $this->project_description;
    }

    /**
     * Set base_url
     *
     * @param string $baseUrl
     * @return Project
     */
    public function setBaseUrl($baseUrl)
    {
        $this->base_url = $baseUrl;
    
        return $this;
    }

    /**
     * Get base_url
     *
     * @return string 
     */
    public function getBaseUrl()
    {
        return $this->base_url;
    }

    /**
     * @ORM\PostLoad
     */
    // TODO: this needs to be made conditional on being on a create or edit form; it's getting called on the main /project page
    public function configureBehat()
    {
        // Build config file location 
        $fs = new Filesystem();
        $project_dir = DIRECTORY_SEPARATOR.'home/melissa/projects'.DIRECTORY_SEPARATOR.$this->id;
        $config_file = 'behat.yml';
        $shell_file = 'jenkins.sh';
        $shell_config = $project_dir.DIRECTORY_SEPARATOR.$shell_file;
        $behat_config = $project_dir.DIRECTORY_SEPARATOR.$config_file;
        $this->repository_uri = $project_dir; // TODO: This should not be displayed on the form
        
        // Get config data
        $settings = array('base_url' => $this->base_url);


        if ($fs->exists($behat_config)) {

            // Update existing file
            $yaml = Yaml::parse($behat_config);
//            $yaml['default']['extensions']['Behat\MinkExtension\Extension']['base_url'] = $settings['base_url'];
            $shell = "#! /bin/bash\nbin/behat";

        } else {

            // Create the file
            $fs->mkdir($project_dir);
            $fs->touch($behat_config);
            $fs->touch($shell_config);
            $yaml = array(
                'default' => array(
                    'paths' => array(
                       'features' => 'features'
                    ),
                'extensions' => array(
                    'Behat\MinkExtension\Extension' => array(
                      'goutte' => null,
                      'selenium2' => null,
                      'base_url' => $settings['base_url'],
                    ),
                  ),
                ),
             );
             $shell = 'Woot!';

        }

        // Write the file
        if ($fs->exists($project_dir. '/bin')) {
        } else {
          $fs->mirror('/home/melissa/example', $project_dir. '/');
          $fs->chmod($project_dir. '/bin/behat', 744);
        }
        $dumper = new Dumper();
        $yaml = $dumper->dump($yaml,5);
        file_put_contents($behat_config, $yaml);
        file_put_contents($shell_config, $shell);
    }
}
