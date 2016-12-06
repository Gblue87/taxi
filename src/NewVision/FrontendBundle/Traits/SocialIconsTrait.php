<?php
namespace NewVision\FrontendBundle\Traits;

trait SocialIconsTrait {


    /**
     * @ORM\Column(name="share_icons", type="boolean", nullable=true)
     */
    protected $shareIcons;

    /**
    * Get shareIcons
    * @return
    */
    public function getShareIcons()
    {
        return $this->shareIcons;
    }

    /**
    * Set shareIcons
    * @return $this
    */
    public function setShareIcons($shareIcons)
    {
        $this->shareIcons = $shareIcons;
        return $this;
    }
}
