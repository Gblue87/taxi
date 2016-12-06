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

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use NewVision\PublishWorkflowBundle\PublishWorkflowTrait;
use NewVision\PublishWorkflowBundle\PublishWorkflowInterface;
use NewVision\SEOBundle\SeoAwareInterface;
use NewVision\SEOBundle\SeoAwareTrait;

/**
 * Airport's entity
 *
 * @ORM\Table(name="airports")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="AirportRepository")
 * @Gedmo\Loggable
 *
 * @package NewVisionAirportsBundle
 * @author  Nikolay Tumbalev <n.tumbalev@newvision.bg>
 */
class Airport implements PublishWorkflowInterface, SeoAwareInterface
{
    use SeoAwareTrait;
    use PublishWorkflowTrait;
    use \NewVision\FrontendBundle\Traits\SocialIconsTrait;
    use \A2lix\TranslationFormBundle\Util\Gedmo\GedmoTranslatable;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="slug", type="string", length=255, nullable=true)
     */
    protected $slug;

    /**
     * @Gedmo\Sortable
     * @Gedmo\Versioned
     * @ORM\Column(name="rank", type="integer")
     */
    protected $rank;

    /**
     * @var string
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="is_homepage", type="boolean", nullable=true)
     */
    protected $isHomepage;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="from_place", type="string", length=255, nullable=true)
     */
    protected $from;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="to_place", type="string", length=255, nullable=true)
     */
    protected $to;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="button_title", type="string", length=250, nullable=true)
     */
    protected $buttonTitle;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="simple_description", type="text", nullable=true)
     */
    protected $simpleDescription;

    /**
     * @var string
     * @Gedmo\Versioned
     * @Gedmo\Translatable
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
     * @ORM\Column(name="reference_no", type="string", length=255, nullable=true)
     */
    protected $referenceNo;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="price", type="string", length=255, nullable=true)
     */
    protected $price;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="double_price", type="string", length=255, nullable=true)
     */
    protected $doublePrice;


    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="tab_description", type="text", nullable=true)
     */
    protected $tabDescription;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="tab_tech", type="text", nullable=true)
     */
    protected $tabTech;

    /**
     * @var string
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="youtube_video", type="string", length=255, nullable=true)
     */
    protected $youTubeVideo;

    /**
     * @ORM\ManyToMany(targetEntity="NewVision\AirportsBundle\Entity\AirportCategory", inversedBy="airports")
     * @ORM\JoinTable(name="airports_categories")
     */
    protected $airportCategories;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="NewVision\AirportsBundle\Entity\AirportTranslation", mappedBy="object", cascade={"persist", "remove"}, indexBy="locale")
     */
    protected $translations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param  string        $title
     * @return Entertainment
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
     * Set createdAt
     *
     * @param  \DateTime     $createdAt
     * @return Entertainment
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param  \DateTime     $updatedAt
     * @return Entertainment
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function __toString()
    {
        return $this->getTitle() ?: 'n/a';
    }

    /**
     * Gets the value of slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Sets the value of slug.
     *
     * @param string $slug the slug
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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the value of description.
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
     * Gets the value of gallery.
     *
     * @return mixed
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * Sets the value of gallery.
     *
     * @param mixed $gallery the gallery
     *
     * @return self
     */
    public function setGallery($gallery)
    {
        $this->gallery = $gallery;

        return $this;
    }

    public function getRoute()
    {
        if ($this->getAirportCategories()->count() > 0) {
            return 'airports_category_airport_view';
        } else {
            return 'airport_without_category';
        }
    }

    public function getRouteParams($params = array())
    {
        return array_merge(array('slug' => $this->getSlug()), $params);
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
     * Gets the value of rank.
     *
     * @return mixed
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Sets the value of rank.
     *
     * @param mixed $rank the rank
     *
     * @return self
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }


    /**
     * Get the value of AirportCategories
     *
     * @return mixed
     */
    public function getAirportCategories()
    {
        return $this->airportCategories;
    }

    /**
     * Set the value of AirportCategories
     *
     * @param mixed airportCategories
     *
     * @return self
     */
    public function setAirportCategories($airportCategories)
    {
        $this->airportCategories = $airportCategories;

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
     * Get the value of Reference No
     *
     * @return mixed
     */
    public function getReferenceNo()
    {
        return $this->referenceNo;
    }

    /**
     * Set the value of Reference No
     *
     * @param mixed referenceNo
     *
     * @return self
     */
    public function setReferenceNo($referenceNo)
    {
        $this->referenceNo = $referenceNo;

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
     * Gets the value of from.
     *
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Sets the value of from.
     *
     * @param string $from the from
     *
     * @return self
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Gets the value of to.
     *
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Sets the value of to.
     *
     * @param string $to the to
     *
     * @return self
     */
    public function setTo($to)
    {
        $this->to = $to;

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
     * Gets the value of isHomepage.
     *
     * @return string
     */
    public function getIsHomepage()
    {
        return $this->isHomepage;
    }

    /**
     * Sets the value of isHomepage.
     *
     * @param string $isHomepage the is homepage
     *
     * @return self
     */
    public function setIsHomepage($isHomepage)
    {
        $this->isHomepage = $isHomepage;

        return $this;
    }
}
