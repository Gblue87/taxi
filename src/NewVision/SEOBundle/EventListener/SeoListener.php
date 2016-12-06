<?php
namespace NewVision\SEOBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use \NewVision\SEOBundle\SeoAwareInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Load object seo.
 *
 * @author Hristo Hristov <hristo.hristov@newvision.bg>
 */
class SeoListener
{
    /**
     * Instance of sonata seo bundle.
     * Used to load object seo.
     *
     * @var Sonata\SeoBundle\Seo\SeoPage
     */
    private $seoPage;

    /**
     * Instance of request stack
     * Used to get current uri
     *
     */
    private $requestStack;

    /**
     * Translator
     */
    private $translator;

    /**
     * The Construcotr.
     *
     * @param Sonata\SeoBundle\Seo\SeoPage $seoPage
     */
    public function __construct($seoPage, $requestStack, TranslatorInterface $translator)
    {
        $this->seoPage = $seoPage;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
    }

    /**
     * Load SEO for object.
     *
     * @param  \NewVision\SEOBundle\Event\SeoEvent $event
     */
    public function onLoadSeo(\NewVision\SEOBundle\Event\SeoEvent $event)
    {
        if ($event->getTitle() && strlen($event->getTitle()) > 0) {
            $seoTitleText = $this->translator->trans('seoTitleText', array(), 'NewVisionFrontendBundle');
            if ($event->haveSuffix) {
                $title = $event->getTitle() . $seoTitleText;
            } else {
                $title = $event->getTitle();
            }

            $this->seoPage->setTitle($title);
        }

        if($event->getDescription() && strlen($event->getDescription()) > 0) {
            $this->seoPage->addMeta('name', 'description', $event->getDescription());
        }
        if($event->getKeywords() && strlen($event->getKeywords()) > 0) {
            $this->seoPage->addMeta('name', 'keywords', $event->getKeywords());
        }
        if($event->getOriginalUrl() && strlen($event->getOriginalUrl()) > 0) {
            $this->seoPage->setLinkCanonical($event->getOriginalUrl());
        } else {
            $this->seoPage->setLinkCanonical($this->requestStack->getCurrentRequest()->getUri());
        }

    }
}