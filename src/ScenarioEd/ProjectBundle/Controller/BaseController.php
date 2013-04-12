<?php

namespace ScenarioEd\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Behat\Behat\DependencyInjection\BehatExtension;
use Behat\Behat\DependencyInjection\Configuration\Loader;
use Symfony\Component\Finder\Finder;

/**
 * Description of BaseController
 *
 * @author lotyrin
 */
class BaseController extends Controller {
    protected $container;
    private $basePath;

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

    protected function loadFeatures($base, $path)
    {
        $gherkin = $this->createContainer($base)->get('gherkin');
        $features = $gherkin->load($path);
        return $features;
    }
}
?>
