<?php
namespace NewVision\TinyMCEBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Twig Extension for TinyMce support.
 *
 * @author Hristo Hristoff <hristo.hristov@newvision.bg>
 */
class NewVisionTinyMCEExtension extends \Twig_Extension
{
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


    public function getFilters()
    {
        return array(
            'graw' => new \Twig_Filter_Method($this, 'galleryInit', array('is_safe' => array('html'))),
        );
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            'tinymce_init' => new \Twig_Function_Method($this, 'tinymceInit', array('is_safe' => array('html'))),
        );
    }

    /**
     * TinyMce initializations
     *
     * @param array $options
     * @return string
     */
    public function galleryInit($description, $template = 'ApplicationSonataMediaBundle:Inline:gallery.html.twig', $repo = 'ApplicationSonataMediaBundle:Gallery')
    {
        $tmpl = $this->getService('templating');
        $em = $this->getService('doctrine')->getManager();
        $repo = $em->getRepository($repo);
        $matched = array();
        $replacements = array();
        preg_match_all('/\{gallery=(\d+)\}/', $description, $matches);

        for ($i = 0; $i < count($matches[0]); $i++) {
            $matched[] = '/\{gallery='.$matches[1][$i].'\}/';
            $gallery = $repo->findOneById($matches[1][$i]);
            if($gallery) {
                $replacements[] = $tmpl->render($template, array('gallery' => $gallery));
            }

        }

        if (count($matched))
            $description = preg_replace($matched, $replacements, $description);

        return $description;
    }


    /**
     * TinyMce initializations
     *
     * @param array $options
     * @return string
     */
    public function tinymceInit($options = array())
    {
        $config = $this->getParameter('new_vision_tiny_mce.config');
        $sec = $this->getService('security.context');
        $session = $this->getService('session');


        $config = array_merge_recursive($config, $options);

        if($sec->getToken() && $sec->getToken()->getUser()) {
            $session->set('AFM_LOGGEDIN', true);
        }

        $this->baseUrl = (!isset($config['base_url']) ? null : $config['base_url']);


        // Update URL to external plugins
        $arr = array();
        foreach ($config['external_plugins'] as $key => &$extPlugin) {
            $arr[$key] = $this->getAssetsUrl($extPlugin['url']);
            $extPlugin['url'] = $this->getAssetsUrl($extPlugin['url']);
        }
        $config['external_plugins'] = $arr;

        return $this->getService('templating')->render('NewVisionTinyMCEBundle::init.html.twig', array(
            'tinymce_config' => preg_replace(
                '/"file_browser_callback":"([^"]+)"\s*/', 'file_browser_callback:$1',
                json_encode($config)
            )
        ));
    }
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'newvision_tinymce';
    }

    /**
     * Get url from config string
     *
     * @param string $inputUrl
     *
     * @return string
     */
    protected function getAssetsUrl($inputUrl)
    {
        /** @var $assets \Symfony\Component\Templating\Helper\CoreAssetsHelper */
        $assets = $this->getService('templating.helper.assets');
        $url = preg_replace('/^asset\[(.+)\]$/i', '$1', $inputUrl);
        if ($inputUrl !== $url) {
            return $assets->getUrl($this->baseUrl . $url);
        }
        return $inputUrl;
    }
}