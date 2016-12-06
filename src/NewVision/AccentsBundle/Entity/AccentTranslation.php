<?php

namespace NewVision\AccentsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

 /**
  *  Entity holding Accent's translations
  *
  * @ORM\Entity
  * @ORM\Table(name="accents_i18n", uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
  *     "locale", "object_id"
  *   })}
  * )
  * @Gedmo\Loggable
  *
  */
class AccentTranslation extends AbstractTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="NewVision\AccentsBundle\Entity\Accent", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;

    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="button_title", type="string", length=255, nullable=true)
     */
    protected $buttonTitle;


    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="url", type="text", nullable=true)
     */
    protected $url;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(length=255, nullable=true)
     */
    protected $target;

    /**
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"})
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id",  onDelete="SET NULL")
     */
    protected $image;

    /**
     * Convinient constructor
     *
     * @param string $url
     * @param string $title
     * @param string $description
     */
    public function __construct($title = null, $description = null, $url = null, $image = null, $target = null, $buttonTitle = null)
    {
        $this->title = $title;
        $this->description = $description;
        $this->url = $url;
        $this->image = $image;
        $this->target = $target;
        $this->buttonTitle = $buttonTitle;
    }


    /**
     * Set title
     *
     * @param string $title
     * @return AccentTranslation
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return AccentTranslation
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Accent
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set image
     *
     * @param integer $image
     * @return Accent
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return integer
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Gets the value of target.
     *
     * @return mixed
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Sets the value of target.
     *
     * @param mixed $target the target
     *
     * @return self
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
    * Get buttonTitle
    * @return
    */
    public function getButtonTitle()
    {
        return $this->buttonTitle;
    }

    /**
    * Set buttonTitle
    * @return $this
    */
    public function setButtonTitle($buttonTitle)
    {
        $this->buttonTitle = $buttonTitle;
        return $this;
    }
}
