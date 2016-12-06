<?php

namespace NewVision\TranslationsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

use NewVision\TranslationsBundle\Manager\TranslationInterface;

/**
 * @ORM\Table(name="translations_unit_translations")
 * @ORM\Entity
 *
 * @Gedmo\Loggable
 *
 * @author Hristo Hristoff <hristo.hristov@newvision.bg>
 */
class Translation implements TranslationInterface
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var NewVision\TranslationsBundle\Entity\File
     *
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="NewVision\TranslationsBundle\Entity\File", inversedBy="translations")
     */
    protected $file;

    /**
     * @var NewVision\TranslationsBundle\Entity\TransUnit
     *
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="NewVision\TranslationsBundle\Entity\TransUnit", inversedBy="translations")
     */
    protected $trans_unit;

    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=10)
     */
    protected $locale;

    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255)
     */
    protected $content;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set transUnit
     *
     * @param NewVision\TranslationsBundle\Entity\TransUnit $transUnit
     */
    public function setTransUnit(\NewVision\TranslationsBundle\Entity\TransUnit $transUnit)
    {
        $this->trans_unit = $transUnit;
    }

    /**
     * Get transUnit
     *
     * @return NewVision\TranslationsBundle\Entity\TransUnit
     */
    public function getTransUnit()
    {
        return $this->trans_unit;
    }

    /**
     * Set locale
     *
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        $this->content = '';
    }

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set content
     *
     * @param text $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Get content
     *
     * @return text
     */
    public function getContent()
    {
        return $this->content;
    }


    /**
     * Set file
     *
     * @param \NewVision\TranslationsBundle\Entity\File $file
     */
    public function setFile(\NewVision\TranslationsBundle\Entity\File $file)
    {
        $this->file = $file;
    }

    /**
     * Get file
     *
     * @return \NewVision\TranslationsBundle\Entity\File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Get createdAt
     *
     * @return datetime $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get updatedAt
     *
     * @return datetime $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function __toString()
    {
        return $this->getContent() ? : 'n/a';
    }
}
