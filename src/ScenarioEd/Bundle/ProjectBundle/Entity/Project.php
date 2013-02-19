<?php

namespace ScenarioEd\Bundle\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Project
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="ScenarioEd\Bundle\ProjectBundle\Entity\ProjectRepository")
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
    private $repository_uri;


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
}
