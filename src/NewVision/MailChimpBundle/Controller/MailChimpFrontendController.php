<?php
/**
 * This file is part of the NewVisionMailChimpBundle.
 *
 * (c) Nikolay Tumbalev <n.tumbalev@newvision.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NewVision\MailChimpBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 *  Frontend controller for the Bundle
 *
 * @package NewVisionMailChimpBundle
 * @author  Nikolay Tumbalev <n.tumbalev@newvision.bg>
 */
class MailChimpFrontendController extends Controller
{
    /**
     * Route("/mailchimp/test", name="mailchimp")
     * Template("NewVisionNewsBundle:Frontend:posts_list.html.twig")
     */
    public function mailChimpAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();

        $mailChimpObject = $em->getRepository('NewVisionMailChimpBundle:MailChimp')->findOneById(1);

        $mc = new \NewVision\MailChimpBundle\Services\MailChimp($mailChimpObject->getApiKey());

        // $subscriberHash = '370af12c994646b70a9b26daf1aae2c6';
        // $deleteMember = $mc->getList($mailChimpObject->getListId())->deleteListMember($subscriberHash);
        // echo "<pre>"; var_dump($deleteMember); echo "</pre>"; exit;

        $listMembers = $mc->getList($mailChimpObject->getListId())->getAllListMembers();
        echo "<pre>"; var_dump($listMembers); echo "</pre>"; exit;

        return array(
        );
    }
}
