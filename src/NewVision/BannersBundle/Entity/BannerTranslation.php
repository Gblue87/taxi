<?php

namespace NewVision\BannersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

 /**
  *  Entity holding Banner's translations
  *
  * @ORM\Entity
  * @ORM\Table(name="banners_i18n", uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
  *     "locale", "object_id"
  *   })}
  * )
  * @Gedmo\Loggable
  *
  */
class BannerTranslation extends AbstractTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="NewVision\BannersBundle\Entity\Banner", inversedBy="translations")
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
     * @ORM\Column(name="sub_title", type="string", length=250, nullable=true)
     */
    protected $subTitle;

    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="label_title", type="string", length=250, nullable=true)
     */
    protected $labelTitle;

    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="label_type", type="string", length=250, nullable=true)
     */
    protected $labelType;

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
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="type", type="string", length=250, options={"default" = "small"}), nullable=true)
     */
    protected $type;

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
    public function __construct($title = null, $url = null, $image = null, $target = null, $buttonTitle = null)
    {
        $this->title = $title;
        $this->url = $url;
        $this->image = $image;
        $this->target = $target;
        $this->buttonTitle = $buttonTitle;
    }


    /**
     * Set title
     *
     * @param string $title
     * @return BannerTranslation
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
     * Set url
     *
     * @param string $url
     * @return Banner
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
     * @return Banner
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

    /**
     * Get the value of Type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of Type
     *
     * @param string type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the value of Sub Title
     *
     * @return string
     */
    public function getSubTitle()
    {
        return $this->subTitle;
    }

    /**
     * Set the value of Sub Title
     *
     * @param string subTitle
     *
     * @return self
     */
    public function setSubTitle($subTitle)
    {
        $this->subTitle = $subTitle;

        return $this;
    }

    /**
     * Get the value of Label Title
     *
     * @return string
     */
    public function getLabelTitle()
    {
        return $this->labelTitle;
    }

    /**
     * Set the value of Label Title
     *
     * @param string labelTitle
     *
     * @return self
     */
    public function setLabelTitle($labelTitle)
    {
        $this->labelTitle = $labelTitle;

        return $this;
    }

    /**
     * Get the value of Label Type
     *
     * @return string
     */
    public function getLabelType()
    {
        return $this->labelType;
    }

    /**
     * Set the value of Label Type
     *
     * @param string labelType
     *
     * @return self
     */
    public function setLabelType($labelType)
    {
        $this->labelType = $labelType;

        return $this;
    }

}
