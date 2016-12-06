<?php

namespace NewVision\SettingsBundle\Manager;

use NewVision\SettingsBundle\Entity\Setting;
/**
 * Manager for settings.
 *
 * @author Nedelin Yordanov <https://github.com/Ch1pStar>
 **/
class SettingsManager{

    protected $em;


    public function __construct($em){
        $this->em = $em;

    }

    public function get($name, $def = null){
    	$item = $this->getSetting($name);

        if($item){
            return $item->getValue();
        }
        return $def;
    }

    public function set($name, $value) {
        $itm = $this->getSetting($name);
        if($itm == null) {
            $itm = new Setting();
            $itm->setName($name);
        }
        $itm->setValue($value);
        $this->em->persist($itm);
        $this->em->flush();
    }


    protected function getSetting($name) {
        $r = $this->em->getRepository('NewVisionSettingsBundle:Setting');
        $item = $r->findBy(array('name' => $name));

        if($item){
            $item = $item[0];
            return $item;
        }
        return null;
    }
}
