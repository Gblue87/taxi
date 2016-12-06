<?php
namespace NewVision\SettingsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="setting")
 * @ORM\Entity
 * @Gedmo\Loggable
 */
class Setting
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(length=150, nullable=true)
     * @Gedmo\Versioned
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Versioned
     */
    private $value;


    /**
     * @ORM\Column(length=150, nullable=true)
     * @Gedmo\Versioned
     */
    private $label;


    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Gedmo\Versioned
     */
    private $visible;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function setLabel($value){
        $this->label = $value;
    }
    public function setVisible($value){
        $this->visible = $value;
    }

    public function getLabel(){
        return $this->label;
    }

    public function getVisible(){
        return $this->visible;
    }

    public function __toString()
    {
        return $this->getname().' : '.$this->getValue();
    }

    public function toArray(){
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'value' => $this->value,
            'label' => $this->label,
            'visible' => $this->visible,
            'created' => $this->created,
            'updated' => $this->updated,
        );
    }
}
