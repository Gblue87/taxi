<?php
namespace NewVision\SEOBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="meta_data")
 * @ORM\Entity
 * @Gedmo\Loggable
 */
class MetaData
{
    use \A2lix\TranslationFormBundle\Util\Gedmo\GedmoTranslatable;

    /** @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer") */
    protected $id;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="keywords", type="string", length=255, nullable=true)
     */
    protected $keywords;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="original_url", type="string", length=255, nullable=true)
     */
    protected $originalUrl;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="extra_properties", type="array", nullable=true)
     */
    protected $extraProperties;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="extra_names", type="array", nullable=true)
     */
    protected $extraNames;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="extra_https", type="array", nullable=true)
     */
    protected $extraHttp;


     /**
     * @ORM\OneToMany(targetEntity="NewVision\SEOBundle\Entity\MetaDataTranslation", mappedBy="object", cascade={"persist", "remove"}, indexBy="locale")
     */
    protected $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    /**
     * Gets the value of id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the value of id.
     *
     * @param mixed $id the id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Sets the value of title.
     *
     * @param mixed $title the title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets the value of title.
     *
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Gets the value of description.
     *
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the value of description.
     *
     * @param mixed $description the description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Gets the value of meta_keywords.
     *
     * @return mixed
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Sets the value of keywords.
     *
     * @param mixed $keywords the   keywords
     *
     * @return self
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }


    /**
     * Gets the value of translations.
     *
     * @return mixed
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Sets the value of translations.
     *
     * @param mixed $translations the translations
     *
     * @return self
     */
    public function setTranslations($translations)
    {
        $this->translations = $translations;

        return $this;
    }

    public function __toString()
    {
        return $this->getTitle() ?: 'n/a';
    }


    /**
     * Gets the value of originalUrl.
     *
     * @return mixed
     */
    public function getOriginalUrl()
    {
        return $this->originalUrl;
    }

    /**
     * Sets the value of originalUrl.
     *
     * @param mixed $originalUrl the original url
     *
     * @return self
     */
    public function setOriginalUrl($originalUrl)
    {
        $this->originalUrl = $originalUrl;

        return $this;
    }

    /**
     * Gets the value of extraProperties.
     *
     * @return mixed
     */
    public function getExtraProperties()
    {
        return $this->extraProperties;
    }

    /**
     * Sets the value of extraProperties.
     *
     * @param mixed $extraProperties the extra properties
     *
     * @return self
     */
    public function setExtraProperties($extraProperties)
    {
        $this->extraProperties = $extraProperties;

        return $this;
    }

    /**
     * Gets the value of extraNames.
     *
     * @return mixed
     */
    public function getExtraNames()
    {
        return $this->extraNames;
    }

    /**
     * Sets the value of extraNames.
     *
     * @param mixed $extraNames the extra names
     *
     * @return self
     */
    public function setExtraNames($extraNames)
    {
        $this->extraNames = $extraNames;

        return $this;
    }

    /**
     * Gets the value of extraHttp.
     *
     * @return mixed
     */
    public function getExtraHttp()
    {
        return $this->extraHttp;
    }

    /**
     * Sets the value of extraHttp.
     *
     * @param mixed $extraHttp the extra http
     *
     * @return self
     */
    public function setExtraHttp($extraHttp)
    {
        $this->extraHttp = $extraHttp;

        return $this;
    }
}