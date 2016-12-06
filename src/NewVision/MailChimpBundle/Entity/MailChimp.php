<?php

namespace NewVision\MailChimpBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * MailChimp's entity
 *
 * @ORM\Table(name="mail_chimp")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="MailChimpRepository")
 * @Gedmo\Loggable
 *
 */
class MailChimp
{
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
     * @ORM\Column(name="title", type="string", length=250, nullable=true)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="api_key", type="string", length=255)
     */
    protected $apiKey;

    /**
     * @ORM\Column(name="list_id", type="string", length=255, nullable=true)
     */
    protected $listId;

    /**
     * @var bool
     * @ORM\Column(name="is_active", type="boolean")
     */
    protected $isActive = false;

    /**
     * @var bool
     * @ORM\Column(name="double_optin", type="boolean")
     */
    protected $doubleOptin = false;

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

    public function __construct()
    {
        $this->lists = new ArrayCollection();
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
     * @return MailChimp
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
     * @param  \DateTime $createdAt
     * @return MailChimp
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
     * @return MailChimp
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
     * Get the value of Api Key
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Set the value of Api Key
     *
     * @param string apiKey
     *
     * @return self
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Get the value of Is Active
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set the value of Is Active
     *
     * @param bool isActive
     *
     * @return self
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get the value of Double Optin
     *
     * @return bool
     */
    public function getDoubleOptin()
    {
        return $this->doubleOptin;
    }

    /**
     * Set the value of Double Optin
     *
     * @param bool doubleOptin
     *
     * @return self
     */
    public function setDoubleOptin($doubleOptin)
    {
        $this->doubleOptin = $doubleOptin;

        return $this;
    }


    /**
     * Get the value of List Id
     *
     * @return mixed
     */
    public function getListId()
    {
        return $this->listId;
    }

    /**
     * Set the value of List Id
     *
     * @param mixed listId
     *
     * @return self
     */
    public function setListId($listId)
    {
        $this->listId = $listId;

        return $this;
    }

}
