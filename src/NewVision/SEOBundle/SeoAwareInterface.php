<?php
namespace NewVision\SEOBundle;

use NewVision\SEOBundle\Entity\MetaData;
/**
 * This interface is responsible to mark a entity to be aware of SEO
 * metadata.
 *
 * @author Hristo Hristoff <hristo.hristov@newvision.bg>
 */
interface SeoAwareInterface
{
    /**
     * Gets the SEO metadata.
     *
     * @return SeoMetadataInterface
     */
    public function getMetaData();
    /**
     * Sets the SEO metadata for this content.
     *
     * @param array|MetaData $metadata
     */
    public function setMetaData($metadata);
}