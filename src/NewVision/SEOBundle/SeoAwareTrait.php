<?php
namespace NewVision\SEOBundle;
/**
 * This trait plugs into your entity to implement SeoAwareInterface
 * with brevity.
 *
 * @author Hristo Hristoff <hristo.hrisov@newvision.bg>
 */
trait SeoAwareTrait
{
    /**
     * @ORM\OneToOne(targetEntity="NewVision\SEOBundle\Entity\MetaData", cascade={"persist", "remove"})
     * @var int
     */
    protected $metaData;

    /**
     * {@inheritDoc}
     */
    public function getMetaData()
    {
        return $this->metaData;
    }
    /**
     * {@inheritDoc}
     */
    public function setMetaData($metaData)
    {
        $this->metaData = $metaData;
    }
}