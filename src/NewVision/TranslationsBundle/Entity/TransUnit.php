<?php

namespace NewVision\TranslationsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

use NewVision\TranslationsBundle\Manager\TransUnitInterface;

/**
 * @ORM\Table(name="translations_unit")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="NewVision\TranslationsBundle\Entity\TransUnitRepository")
 *
 * @author Hristo Hristoff <hristo.hristov@newvision.bg>
 */
class TransUnit implements TransUnitInterface
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
     * @var string
     *
     * @ORM\Column(name="key_code", type="string", length=255)
     */
    protected $key;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    protected $domain;

    /**
     * @var Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="NewVision\TranslationsBundle\Entity\Translation", mappedBy="trans_unit")
     */
    protected $translations;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * Construct.
     */
    public function __construct()
    {
        $this->domain = 'messages';
        $this->translations = new ArrayCollection();
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
     * Set key name
     *
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * Get key name
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set domain
     *
     * @param string $domain
     */
    public function setDomain($domain)
    {
      $this->domain = $domain;
    }

    /**
     * Get domain
     *
     * @return string
     */
    public function getDomain()
    {
      return $this->domain;
    }

    /**
     * Remove translations
     *
     * @param NewVision\TranslationsBundle\Entity\Translation $translations
     */
    public function removeTranslation(\NewVision\TranslationsBundle\Entity\Translation $translation)
    {
        $this->translations->removeElement($translation);
    }

    /**
     * Get translations
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Return true if this object has a translation for the given locale.
     *
     * @param string $locale
     * @return boolean
     */
    public function hasTranslation($locale)
    {
        return null !== $this->getTranslation($locale);
    }

    /**
     * Return the content of translation for the given locale.
     *
     * @param string $locale
     * @return NewVision\TranslationsBundle\Entity\Translation
     */
    public function getTranslation($locale)
    {
        foreach ($this->getTranslations() as $translation) {

            if ($translation->getLocale() == $locale) {

                return $translation;
            }
        }

        return null;
    }

    /**
     * Set translations collection
     *
     * @param Collection $collection
     */
    public function setTranslations(ArrayCollection $collection)
    {
        $this->translations = new ArrayCollection();

        foreach ($collection as $translation) {
            $this->addTranslation($translation);
        }
    }

    /**
     * Add translations
     *
     * @param NewVision\TranslationsBundle\Entity\Translation $translations
     */
    public function addTranslation(\NewVision\TranslationsBundle\Entity\Translation $translation)
    {
        $translation->setTransUnit($this);

        $this->translations[] = $translation;
    }

    /**
     * Return transaltions with  not blank content.
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function filterNotBlankTranslations()
    {
        return $this->getTranslations()->filter(function ($translation) {
            $content = $translation->getContent();
            return !empty($content);
        });
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
        return $this->getKey() ? : 'n/a';
    }
}
