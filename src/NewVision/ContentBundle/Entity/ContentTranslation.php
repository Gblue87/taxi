<?php

namespace NewVision\ContentBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

/**
 *  Entity holding Content's translations
 *
 * @ORM\Entity
 * @ORM\Table(name="content_i18n", uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
 *     "locale", "object_id"
 *   }), @ORM\UniqueConstraint(name="slug_unique_idx", columns={"slug", "locale"})}
 * )
 * @Gedmo\Loggable
 *
 */
class ContentTranslation extends AbstractTranslation
{

    /**
     * @ORM\ManyToOne(targetEntity="NewVision\ContentBundle\Entity\Content", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(length=255, nullable=true)
     */
    protected $title;

    /**
    * @Gedmo\Slug(fields={"title"}, updatable=false, unique=false)
    * @ORM\Column(length=255, nullable=true)
    */
    protected $slug;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"})
     */
    protected $headerImage;

    /**
     * Convinient constructor
     *
     * @param string $locale
     * @param string $title
     * @param string $description
     */
    public function __construct($locale = null, $title = null, $slug = null, $description = null)
    {
        $this->locale = $locale;
        $this->title = $title;
        $this->description = $description;
        $this->slug = $slug;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * Gets the value of headerImage.
     *
     * @return Media
     */
    public function getHeaderImage()
    {
        return $this->headerImage;
    }

    /**
     * Sets the value of headerImage.
     *
     * @param Media $headerImage the header image
     *
     * @return self
     */
    public function setHeaderImage($headerImage)
    {
        $this->headerImage = $headerImage;

        return $this;
    }
}
