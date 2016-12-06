<?php

namespace NewVision\SettingsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SettingsController extends Controller
{
   /**
	* @Template("NewVisionSettingsBundle::render.html.twig")
	*/
	public function renderAction($key)
	{
		$settingsManager = $this->get('newvision.settings_manager');
		$content = $settingsManager->get($key);

		return array('content' => $content);
	}
}
