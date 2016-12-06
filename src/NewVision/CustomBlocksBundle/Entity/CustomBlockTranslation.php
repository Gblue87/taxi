<?php
/**
 * This file is part of the NewVisionCustomBlocksBundle.
 *
 * (c) Nikolay Tumbalev <n.tumbalev@newvision.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NewVision\CustomBlocksBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

 /**
  *  Entity holding CustomBlock's translations
  *
  * @ORM\Entity
  * @ORM\Table(name="custom_blocks_i18n", uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
  *     "locale", "object_id"
  *   })}
  * )
  * @Gedmo\Loggable
  *
  */
class CustomBlockTranslation extends AbstractTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="NewVision\CustomBlocksBundle\Entity\CustomBlock", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;

    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * Convinient constructor
     *
     * @param string $url
     * @param string $title
     * @param string $description
     */
    public function __construct($title = null, $description = null)
    {
        $this->title = $title;
        $this->description = $description;
    }


    /**
     * Set title
     *
     * @param string $title
     * @return CustomBlockTranslation
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
     * Set description
     *
     * @param string $description
     * @return CustomBlockTranslation
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
