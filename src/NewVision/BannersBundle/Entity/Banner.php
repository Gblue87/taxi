<?php

namespace NewVision\BannersBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

use NewVision\PublishWorkflowBundle\PublishWorkflowTrait;
use NewVision\PublishWorkflowBundle\PublishWorkflowInterface;

/**
 * Banner's entity
 *
 * @ORM\Table(name="banners")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="BannerRepository")
 * @Gedmo\Loggable
 * @Assert\Callback(methods={"checkImage"})
 *
 */
class Banner implements PublishWorkflowInterface
{
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
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="title", type="string", length=250, nullable=true)
     */
    protected $title;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="button_title", type="string", length=250, nullable=true)
     */
    protected $buttonTitle;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="sub_title", type="string", length=250, nullable=true)
     */
    protected $subTitle;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="label_title", type="string", length=250, nullable=true)
     */
    protected $labelTitle;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="label_type", type="string", length=250, nullable=true)
     */
    protected $labelType;

    /**
     * @var integer
     * @Gedmo\Sortable
     * @Gedmo\Versioned
     * @ORM\Column(name="rank", type="integer")
     */
    protected $rank;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="url", type="text", nullable=true)
     */
    protected $url;

    /**
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"})
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id",  onDelete="SET NULL")
     */
    protected $image;

    /**
     * @var string
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="target", type="string", length=250, options={"default" = "_self"}), nullable=true)
     */
    protected $target;

    /**
     * @var string
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="type", type="string", length=250, options={"default" = "small"}), nullable=true)
     */
    protected $type;

    /**
     * @var \DateTime
     *
     * @Gedmo\Versioned
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Versioned
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="NewVision\BannersBundle\Entity\BannerTranslation", mappedBy="object", cascade={"persist", "remove"}, indexBy="locale")
     */
    protected $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    /**
     * Check if the uploaded banner fit to the associated zone
     *
     * @param ExecutionContextInterface $context
     */
    public function checkImage(ExecutionContextInterface $context)
    {
        foreach ($this->getTranslations() as $translation) {
            if (!$translation->getImage() && $translation->getTitle()) {
                $context->addViolationAt('image', "Изображението е задължително!");
            }
        }
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
     * @param  string $title
     * @return Banner
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
        if($this->title === NULL || strlen($this->title) == 0) {
            foreach ($this->translations as $translation) {
                if($translation->getTitle() && strlen($translation->getTitle()) != 0) {
                    return $translation->getTitle();
                }
            }
        }
        return $this->title;
    }

    /**
     * Set rank
     *
     * @param  integer $rank
     * @return self
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return integer
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set url
     *
     * @param  string $url
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
     * Set target
     *
     * @param  string $target
     * @return target
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set createdAt
     *
     * @param  \DateTime $createdAt
     * @return Banner
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
     * @param  \DateTime $updatedAt
     * @return Banner
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
     * Gets the value of image.
     *
     * @return Media
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Sets the value of image.
     *
     * @param Media $image the image
     *
     * @return self
     */
    protected function setImage($image)
    {
        $this->image = $image;

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
