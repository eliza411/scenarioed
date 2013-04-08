<?php

namespace ScenarioEd\ProjectBundle\Entity;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;


use Symfony\Component\Validator\Constraints as Assert;

/**
 * Feature
 *
 */
class Feature
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $file;

    /**
     * @var text
     * @Assert\NotBlank()
     */
    private $contents;

    function __construct($file) {
        $this->file = $file;
        if (is_readable($file)) {
            $this->contents = file_get_contents($file);
        }
    }

    /**
     * Set file
     *
     * @param string $file
     * @return Feature
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string 
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set contents
     *
     * @param string $contents
     * @return Feature
     */
    public function setContents($contents)
    {
        $this->contents = $contents;
        file_put_contents($this->file, $this->contents);
    
        return $this;
    }

    /**
     * Get contents
     *
     * @return string 
     */
    public function getContents()
    {
        return $this->contents;
    }

    public function delete()
    {
        $fs = new Filesystem();
        $fs->remove($this->file);
    }

    public function create()
    {
        $fs = new Filesystem();
        $fs->touch($this->file);
    }
}
