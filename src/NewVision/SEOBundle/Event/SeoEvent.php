<?php
namespace NewVision\SEOBundle\Event;

use \Stneik\SEOBundle\SeoAwareInterface;
use Symfony\Component\EventDispatcher\Event;


/**
 * SeoEvent. Used to dispatch object to SeoListener.
 *
 * @author Hristo Hristov <hristo.hristov@stneik.bg>
 */
class SeoEvent extends Event
{
    /**
     * Object which implements SeoAwareInterface
     *
     * @var Stneik\SEOBundle\SeoAwareInterface
     */
    private $data;

    /**
     * Meta Title
     * @var string
     */
    private $title;

    /**
     * Meta Description
     * @var string
     */
    private $description;

    /**
     * Meta Keywords
     * @var string
     */
    private $keywords;

    /**
     * Canonical url
     * @var string
     */
    private $originalUrl;

    /**
     * We have to set suffix?
     * @var boolean
     */
    public $haveSuffix;

    /**
     * The Constructor.
     *
     * @param Stneik\SEOBundle\SeoAwareInterface $data
     */
    public function __construct($data = null, $suffix = true)
    {
        $this->haveSuffix = $suffix;
        $this->data = $data;
        if($data !== null) {
            $interfaces = class_implements($data);
            if(isset($interfaces['NewVision\SEOBundle\SeoAwareInterface'])) {
                $metaData = $data->getMetaData();

                if(!$metaData) {
                    $this->setTitle($data->getTitle());
                    return;
                }

                if ($metaData->getTitle() && strlen($metaData->getTitle()) > 0) {
                    $this->setTitle($metaData->getTitle());
                }else{
                    $this->haveSuffix = true;
                    $this->setTitle($data->getTitle());

                    return;
                }

                if($metaData->getDescription() && strlen($metaData->getDescription()) > 0) {
                    $this->setDescription($metaData->getDescription());
                }
                if($metaData->getKeywords() && strlen($metaData->getKeywords()) > 0) {
                    $this->setKeywords($metaData->getKeywords());
                }
                if($metaData->getOriginalUrl() && strlen($metaData->getOriginalUrl()) > 0) {
                    $this->setOriginalUrl($metaData->getOriginalUrl());
                }
            }

        }
    }

    /**
     * Gets the value of data.
     *
     * @return Stneik\SEOBundle\SeoAwareInterface
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Gets the Meta Title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the Meta Title.
     *
     * @param string $title the title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets the Meta Description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the Meta Description.
     *
     * @param string $description the description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Gets the Meta Keywords.
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Sets the Meta Keywords.
     *
     * @param string $keywords the keywords
     *
     * @return self
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Gets the Canonical url.
     *
     * @return string
     */
    public function getOriginalUrl()
    {
        return $this->originalUrl;
    }

    /**
     * Sets the Canonical url.
     *
     * @param string $originalUrl the original url
     *
     * @return self
     */
    public function setOriginalUrl($originalUrl)
    {
        $this->originalUrl = $originalUrl;

        return $this;
    }
}