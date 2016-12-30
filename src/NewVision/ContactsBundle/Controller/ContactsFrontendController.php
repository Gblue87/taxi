<?php
/**
 * This file is part of the NewVisionContactsBundle.
 *
 * (c) Nikolay Tumbalev <n.tumbalev@newvision.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NewVision\ContactsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 *  Custom fronted controller
 *
 * @package NewVisionContactsBundle
 * @author  Nikolay Tumbalev <n.tumbalev@newvision.bg>
 */
class ContactsFrontendController extends Controller
{
    /**
     * @Route("/contact-us{trailingSlash}", name="contacts", requirements={"trailingSlash" = "[/]{0,1}"}, defaults={"trailingSlash" = "/"})
     * @Template("NewVisionContactsBundle:Frontend:contacts.html.twig")
     */
    public function contactsAction(Request $request, $item = null)
    {
        if (substr(urldecode($request->getUri()), -1) != '/') {
            return $this->redirect($request->getUri().'/');
        }
        if ($request->get('_route') == 'contact_success' && $request->headers->get('referer') == null) {
            return $this->redirect($this->generateUrl('contacts')).'/';
        }

        $em = $this->getDoctrine()->getManager();
        $translator = $this->get('translator');
        $settings = $this->get('newvision.settings_manager');
        $action = array(
            'action' => $this->generateUrl('contacts', array(), true)
        );

        $path = null;

        $content = $em->getRepository('NewVisionContentBundle:Content')->findOneById(4);
        if (!$content) {
            throw $this->createNotFoundException("Page not found");
        }

        $breadCrumbs = array($content->getTitle() => null);

        $form = $this->createForm('contacts', null, $action);

        if ($request->isMethod('POST')) {
            $this->get('session')->getFlashBag()->clear();
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();

                $adminMessage = \Swift_Message::newInstance()
                    ->setSubject($translator->trans('contact.contact_subject', array(), 'NewVisionFrontendBundle'))
                    ->setFrom($settings->get('sender_email'))
                    ->setTo(explode(',', $settings->get('contact_email')))
                    ->setBody(
                        $this->renderView(
                            'NewVisionContactsBundle:Email:contact_mail.html.twig', array(
                                'data' => $data
                            )
                        ),
                        'text/html'
                    )
                ;

                $userMessage = \Swift_Message::newInstance()
                    ->setSubject($translator->trans('contact.contact_user_subject', array(), 'NewVisionFrontendBundle'))
                    ->setFrom($settings->get('sender_email'))
                    ->setTo($data['email'])
                    ->setBody(
                        $this->renderView(
                            'NewVisionContactsBundle:Email:contact_mail.html.twig', array(
                                'data' => $data
                            )
                        ),
                        'text/html'
                    )
                ;

                $mailer = $this->get('mailer');
                $mailer->send($adminMessage);
                $mailer->send($userMessage);

                $this->get('session')->getFlashBag()->add('success', 'Your message has been sent.');
            } else {
                $this->get('session')->getFlashBag()->add('error', 'Your message has not been sent.');
            }
        }

        $dispatcher = $this->get('event_dispatcher');
        $event = new \NewVision\SEOBundle\Event\SeoEvent($content);
        $dispatcher->dispatch('newvision.seo', $event);


        $this->get('newvision.og_tags')->loadOgTags($content);


        return array(
            'form'        => $form->createView(),
            'content'     => $content,
            'breadCrumbs' => $breadCrumbs,
        );
    }
}
