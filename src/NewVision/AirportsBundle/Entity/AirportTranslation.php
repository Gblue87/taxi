<?php
/**
 * This file is part of the NewVisionAirportsBundle.
 *
 * (c) Nikolay Tumbalev <n.tumbalev@newvision.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NewVision\AirportsBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

/**
 *  Entity holding Airport's translations
 *
 * @ORM\Entity
 * @ORM\Table(name="airports_i18n", uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
 *     "locale", "object_id"
 *   }), @ORM\UniqueConstraint(name="slug_unique_idx", columns={"slug", "locale"})}
 * )
 * @Gedmo\Loggable
 *
 * @package NewVisionAirportsBundle
 * @author  Nikolay Tumbalev <n.tumbalev@newvision.bg>
 */
class AirportTranslation extends AbstractTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="NewVision\AirportsBundle\Entity\Airport", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Slug(fields={"title"}, separator="-", updatable=false, unique=false)
     * @ORM\Column(name="slug", type="string", length=255, nullable=true)
     */
    protected $slug;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="button_title", type="string", length=250, nullable=true)
     */
    protected $buttonTitle;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="simple_description", type="text", nullable=true)
     */
    protected $simpleDescription;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id",  onDelete="SET NULL")
     */
    protected $image;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Gallery")
     * @ORM\JoinColumn(name="gallery_id", referencedColumnName="id",  onDelete="SET NULL")
     */
    protected $gallery;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="price", type="string", length=255, nullable=true)
     */
    protected $price;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="double_price", type="string", length=255, nullable=true)
     */
    protected $doublePrice;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="tab_description", type="text", nullable=true)
     */
    protected $tabDescription;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="tab_tech", type="text", nullable=true)
     */
    protected $tabTech;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="youtube_video", type="string", length=255, nullable=true)
     */
    protected $youTubeVideo;

    /**
     * Gets the value of Title.
     *
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the value of Title.
     *
     * @param mixed $title the Title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets the value of slug.
     *
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Sets the value of slug.
     *
     * @param mixed $slug the slug
     *
     * @return self
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
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
     * Gets the value of simpleDescription.
     *
     * @return mixed
     */
    public function getSimpleDescription()
    {
        return $this->simpleDescription;
    }

    /**
     * Sets the value of simpleDescription.
     *
     * @param mixed $simpleDescription the simple description
     *
     * @return self
     */
    public function setSimpleDescription($simpleDescription)
    {
        $this->simpleDescription = $simpleDescription;

        return $this;
    }

    /**
     * Gets the value of image.
     *
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Sets the value of image.
     *
     * @param mixed $image the image
     *
     * @return self
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get the value of Gallery
     *
     * @return mixed
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * Set the value of Gallery
     *
     * @param mixed gallery
     *
     * @return self
     */
    public function setGallery($gallery)
    {
        $this->gallery = $gallery;

        return $this;
    }

    /**
     * Get the value of You Tube Video
     *
     * @return string
     */
    public function getYouTubeVideo()
    {
        return $this->youTubeVideo;
    }

    /**
     * Set the value of You Tube Video
     *
     * @param string youTubeVideo
     *
     * @return self
     */
    public function setYouTubeVideo($youTubeVideo)
    {
        $this->youTubeVideo = $youTubeVideo;

        return $this;
    }

    /**
     * Get the value of Price
     *
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set the value of Price
     *
     * @param mixed price
     *
     * @return self
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get the value of Tab Description
     *
     * @return mixed
     */
    public function getTabDescription()
    {
        return $this->tabDescription;
    }

    /**
     * Set the value of Tab Description
     *
     * @param mixed tabDescription
     *
     * @return self
     */
    public function setTabDescription($tabDescription)
    {
        $this->tabDescription = $tabDescription;

        return $this;
    }

    /**
     * Get the value of Tab Tech
     *
     * @return mixed
     */
    public function getTabTech()
    {
        return $this->tabTech;
    }

    /**
     * Set the value of Tab Tech
     *
     * @param mixed tabTech
     *
     * @return self
     */
    public function setTabTech($tabTech)
    {
        $this->tabTech = $tabTech;

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
     * Sets the value of doublePrice.
     *
     * @param mixed $doublePrice the double price
     *
     * @return self
     */
    public function setDoublePrice($doublePrice)
    {
        $this->doublePrice = $doublePrice;

        return $this;
    }

    /**
     * Gets the value of doublePrice.
     *
     * @return mixed
     */
    public function getDoublePrice()
    {
        return $this->doublePrice;
    }
}
