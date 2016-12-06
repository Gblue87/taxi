<?php

namespace NewVision\NotificationsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

 /**
  *  Entity holding Notification's translations
  *
  * @ORM\Entity
  * @ORM\Table(name="notifications_i18n", uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
  *     "locale", "object_id"
  *   })}
  * )
  * @Gedmo\Loggable
  *
  */
class NotificationTranslation extends AbstractTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="NewVision\NotificationsBundle\Entity\Notification", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="sub_title", type="string", length=250, nullable=true)
     */
    protected $subTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="button_title", type="string", length=250, nullable=true)
     */
    protected $buttonTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="text", nullable=true)
     */
    protected $url;

    /**
     * @ORM\Column(length=255, nullable=true)
     */
    protected $target;

    /**
     * Set title
     *
     * @param string $title
     * @return NotificationTranslation
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
     * @return Notification
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
     * Get the value of Button Title
     *
     * @return string
     */
    public function getButtonTitle()
    {
        return $this->buttonTitle;
    }

    /**
     * Set the value of Button Title
     *
     * @param string buttonTitle
     *
     * @return self
     */
    public function setButtonTitle($buttonTitle)
    {
        $this->buttonTitle = $buttonTitle;

        return $this;
    }

}
