<?php
/**
 * This file is part of the NewVisionFrontendBundle.
 *
 * (c) Nikolay Tumbalev <n.tumbalev@newvision.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NewVision\CoreBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Twig Extensions
 *
 * @package NewVisionFrontendBundle
 * @author  Nikolay Tumbalev <n.tumbalev@newvision.bg>
 */
class NewVisionTwigExtensions extends \Twig_Extension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            //new \Twig_SimpleFunction('functionName', array($this, 'callMethod'), array('is_safe' => array('html'))),
        );
    }

    public function getFilters()
    {
        return array(
            'bundle_exists' => new \Twig_Filter_Method($this, 'bundleExists', array('is_safe' => array('html'))),
        );
    }

    /**
     * Check if bundle is registered
     *
     * @param string $description
     * @return string
     */
    public function bundleExists($bundleName)
    {
        $bundles = $this->container->getParameter('kernel.bundles');
        if (array_key_exists($bundleName, $bundles)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Container
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Asset Base Url
     * Used to over ride the asset base url (to not use CDN for instance)
     *
     * @var String
     */
    protected $baseUrl;

    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * Gets a service.
     *
     * @param string $id The service identifier
     *
     * @return object The associated service
     */
    public function getService($id)
    {
        return $this->container->get($id);
    }
    /**
     * Get parameters from the service container
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getParameter($name)
    {
        return $this->container->getParameter($name);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'newvision_twig_extensions';
    }
}
