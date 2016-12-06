<?php
namespace NewVision\NewsletterBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="newsletter")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="NewsletterRepository")
 * @Gedmo\Loggable
 */
class Newsletter
{

    /** @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer") */
    protected $id;

    /**
     * @Assert\NotBlank
     * @Assert\Email
     * @ORM\Column(name="title", type="string", length=255, unique = true)
     */
    protected $email;

    /**
     * @var string
     * @ORM\Column(name="mailchimp_status", type="string", length=255, nullable=true)
     */
    protected $mailChimpStatus;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    public function getId()
    {
        return $this->id;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($value)
    {
        $this->email = $value;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function __toString()
    {
        return $this->getEmail() ? : 'n/a';
    }


    /**
     * Get the value of Mail Chimp Status
     *
     * @return string
     */
    public function getMailChimpStatus()
    {
        return $this->mailChimpStatus;
    }

    /**
     * Set the value of Mail Chimp Status
     *
     * @param string mailChimpStatus
     *
     * @return self
     */
    public function setMailChimpStatus($mailChimpStatus)
    {
        $this->mailChimpStatus = $mailChimpStatus;

        return $this;
    }

}
