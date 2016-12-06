<?php
/**
 * This file is part of NewVisionPublishWorkflowBundle.
 *
 * (c) Nikolay Tumbalev <n.tumbalev@newvision.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NewVision\PublishWorkflowBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="publish_workflow")
 * @Gedmo\Loggable
 *
 * @package NewVisionPublishWorkflowBundle
 * @author  <n.tumbalev@newvision.bg>
 */
class PublishWorkflow
{
    use \A2lix\TranslationFormBundle\Util\Gedmo\GedmoTranslatable;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * Starting date of publication
     *
     * @var \DateTime
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="from_date", type="datetime", nullable=true)
     */
    protected $fromDate;

    /**
     * End date of publication
     *
     * @var \DateTime
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="to_date", type="datetime", nullable=true)
     */
    protected $toDate;

    /**
     * Is it active or not
     *
     * @var boolean
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="is_active", type="boolean", options={"default" = 0})
     */
    protected $isActive;

    /**
     * Is it hidden or not
     *
     * @var boolean
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="is_hidden", type="boolean", options={"default" = 0})
     */
    protected $isHidden;

    /**
     * Creation date and time
     *
     * @var \DateTime
     *
     * @Gedmo\Versioned
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * Last update date and time
     *
     * @var \DateTime
     *
     * @Gedmo\Versioned
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * Gets the value of id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the Starting date of publication.
     *
     * @return \DateTime
     */
    public function getFromDate()
    {
        return $this->fromDate;
    }

    /**
     * Sets the Starting date of publication.
     *
     * @param \DateTime $fromDate the from date
     *
     * @return self
     */
    public function setFromDate($fromDate)
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    /**
     * Gets the End date of publication.
     *
     * @return \DateTime
     */
    public function getToDate()
    {
        return $this->toDate;
    }

    /**
     * Sets the End date of publication.
     *
     * @param \DateTime $toDate the to date
     *
     * @return self
     */
    public function setToDate($toDate)
    {
        $this->toDate = $toDate;

        return $this;
    }

    /**
     * Gets the Is it active or not.
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Sets the Is it active or not.
     *
     * @param boolean $isActive the is active
     *
     * @return self
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Gets the value of isHidden.
     *
     * @return mixed
     */
    public function getIsHidden()
    {
        return $this->isHidden;
    }

    /**
     * Sets the value of isHidden.
     *
     * @param mixed $isHidden the is  hidden
     *
     * @return self
     */
    public function setIsHidden($isHidden)
    {
        $this->isHidden = $isHidden;

        return $this;
    }

    /**
     * Gets the value of created_at.
     *
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Sets the value of created_at.
     *
     * @param mixed $created_at the created  at
     *
     * @return self
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Gets the value of updated_at.
     *
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Sets the value of updated_at.
     *
     * @param mixed $updated_at the updated  at
     *
     * @return self
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
