<?php

namespace NewVision\NewsletterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use NewVision\NewsletterBundle\Entity\Newsletter;

class NewsletterFrontendController extends Controller
{
    /**
     * @Route("/newsletter", name="newsletter")
     */
    public function newsletterAction(Request $request)
    {
        $trans = $this->get('translator');
        $email = $request->request->get('email');

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = strtolower($email);
            $em = $this->getDoctrine()->getManager();
            $newsletter = $em->getRepository('NewVisionNewsletterBundle:Newsletter')->findOneByEmail($email);

            if (!$newsletter) {
                $newsletter = new Newsletter();
                $newsletter->setEmail($email);
                $em->persist($newsletter);
                $em->flush();

                $response = array(
                    'success' => true,
                    'html' => $trans->trans('newsletter.success_subscribe', array(), 'messages')
                );
            } else {
                $response = array(
                    'success' => false,
                    'html' => $trans->trans('newsletter.existing_mail', array(), 'messages')
                );
            }

            try {
                $mailChimpObject = $em->getRepository('NewVisionMailChimpBundle:MailChimp')->findOneById(1);
                if ($mailChimpObject != null && $mailChimpObject->getIsActive() == true && $mailChimpObject->getApiKey() != null && $mailChimpObject->getApiKey() != '') {
                    $mc = new \NewVision\MailChimpBundle\Services\MailChimp($mailChimpObject->getApiKey());
                    $status = 'subscribed';
                    if ($mailChimpObject->getDoubleOptin() == true) {
                        $status = 'pending';
                    }

                    if ($newsletter->getMailChimpStatus() == null) {
                        $createListMember = $mc->getList($mailChimpObject->getListId())->editListMember($email, $status);
                        $newsletter->setMailChimpStatus($status);
                    } else {
                        if ($newsletter->getMailChimpStatus() == 'unsubscribed') {
                            $editListMember = $mc->getList($mailChimpObject->getListId())->editListMember($email, $status);
                            $newsletter->setMailChimpStatus($status);
                        }
                    }
                }

                $em->persist($newsletter);
                $em->flush();
            } catch (\Exception $e) {
                $this->sendMail($e->getMessage());
            }
        } else {
            $response = array(
                'success' => false,
                'html' => $trans->trans('newsletter.not_valid_email_format', array(), 'messages')
            );
        }

        return new JsonResponse($response);
    }

    private function sendMail($msg)
    {
        $settings = $this->get('newvision.settings_manager');
        $mailer = $this->get('mailer');
        $message = \Swift_Message::newInstance()
                    ->setSubject('Error in MailChimp')
                    ->setFrom($settings->get('sender_email'))
                    ->setTo(explode(',', $settings->get('contact_email')))
                    ->setBody(
                        $msg
                    )
                ;
        $mailer->send($message);
    }
}
