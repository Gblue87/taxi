<?php

namespace NewVision\TranslationsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

use NewVision\TranslationsBundle\Manager\FileInterface;

/**
 * @ORM\Table(name="translations_file")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="NewVision\TranslationsBundle\Entity\FileRepository")
 *
 * @author Hristo Hristoff <hristo.hristov@newvision.bg>
 */
class File implements FileInterface
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
     * @ORM\Column(type="string", length=255)
     */
    protected $domain;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10)
     */
    protected $locale;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10)
     */
    protected $extention;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    protected $path;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected $hash;

    /**
     * @var Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="NewVision\TranslationsBundle\Entity\Translation", mappedBy="file")
     */
    protected $translations;

    /**
     * Construct.
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * Set locale
     *
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
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
     * Set extention
     *
     * @param string $extention
     */
    public function setExtention($extention)
    {
        $this->extention = $extention;
    }

    /**
     * Get extention
     *
     * @return string
     */
    public function getExtention()
    {
        return $this->extention;
    }

    /**
     * Set path
     *
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set file name
     *
     * @param string $name
     */
    public function setName($name)
    {
        list($domain, $locale, $extention) = explode('.', $name);

        $this->domain = $domain;
        $this->locale = $locale;
        $this->extention = $extention;
    }

    /**
     * Get file name
     *
     * @return string
     */
    public function getName()
    {
        return sprintf('%s.%s.%s', $this->domain, $this->locale, $this->extention);
    }

    /**
     * Set hash
     *
     * @return string
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Add translation
     *
     * @param NewVision\TranslationsBundle\Entity\Translation $translation
     */
    public function addTranslation(\NewVision\TranslationsBundle\Entity\Translation $translation)
    {
        $translation->setFile($this);

        $this->translations[] = $translation;
    }

    /**
     * Get translations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    public function __toString()
    {
        return $this->getDomain() ? : 'n/a';
    }
}