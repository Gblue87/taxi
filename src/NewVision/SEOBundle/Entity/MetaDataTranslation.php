<?php

namespace NewVision\SEOBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

/**
 * NewVision\MenuBundle\Entity\MenuTranslation.php
 *
 * @ORM\Entity
 * @ORM\Table(name="meta_data_i18n", uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
 *     "locale", "object_id"
 *   })}
 * )
 * @Gedmo\Loggable
 */
class MetaDataTranslation extends AbstractTranslation
{

    /**
     * @ORM\ManyToOne(targetEntity="NewVision\SEOBundle\Entity\MetaData", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(length=255, nullable=true)
     */
    protected $title;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(length=255, nullable=true)
     */
    protected $description;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(length=255, nullable=true)
     */
    protected $keywords;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="original_url", type="string", length=255, nullable=true)
     */
    protected $originalUrl;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="extra_properties", type="array", nullable=true)
     */
    protected $extraProperties;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="extra_names", type="array", nullable=true)
     */
    protected $extraNames;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="extra_https", type="array", nullable=true)
     */
    protected $extraHttp;

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

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
     * Gets the value of keywords.
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
     * @param mixed $keywords the keywords
     *
     * @return self
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
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
}