<?php

namespace NewVision\CoreBundle\Services;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OgTagsService
{
    protected $container, $requestStack;

    public function __construct(ContainerInterface $container, RequestStack $requestStack)
    {
        $this->container = $container;
        $this->requestStack = $requestStack;
    }

    public function loadOgTags($object, $params = array())
    {
        $request = $this->requestStack->getCurrentRequest();
        $seoSerivice = $this->container->get('sonata.seo.page');
        $translator = $this->container->get('translator');

        $metaData = null;
        if (is_callable($object, 'getMetaData') && $object->getMetaData() != null) {
            $metaData = $object->getMetaData();
        }

        //check for title
        if ($metaData && $metaData->getTitle()) {
            $seoSerivice->addMeta('property', 'og:title', $metaData->getTitle());
        } else {
            $seoSerivice->addMeta('property', 'og:title', $object->getTitle());
        }
        //check for description
        if (!isset($params['description'])) {
            if ($metaData && $metaData->getDescription()) {
                $seoSerivice->addMeta('property', 'og:description', $metaData->getDescription());
            } else {
                if (is_callable(array($object, 'getSimpleDecription'))) {
                    if (trim($object->getSimpleDecription())) {
                        $simpleDecription = strip_tags($object->getSimpleDescription());
                        $s = substr($simpleDecription, 0, 200);
                        $simpleDecription = substr($s, 0, strrpos($s, ' ')) . '...';
                        $seoSerivice->addMeta('property', 'og:description', $simpleDescription);
                    }
                }elseif(is_callable(array($object, 'getDescription'))){
                    if (trim($object->getDescription())) {
                        $description = strip_tags($object->getDescription());
                        $s = substr($description, 0, 200);
                        $description = substr($s, 0, strrpos($s, ' ')) . '...';
                        $seoSerivice->addMeta('property', 'og:description', $description);
                    }
                }
            }
        }else{
            $seoSerivice->addMeta('property', 'og:description', $params['description']);
            unset($params['description']);
        }

        if (!isset($params['type'])) {
            $seoSerivice->addMeta('property', 'og:type', 'article');
        } else {
            $seoSerivice->addMeta('property', 'og:type', $params['type']);
            unset($params['type']);
        }

        $seoSerivice
            ->addMeta('property', 'og:url',  $request->getUri())
            ->addMeta('property', 'og:site_name', $translator->trans('site_name', array(), 'messages'));

        if (!isset($params['image_url'])) {
            $translation = $object->getTranslations()->get($request->getLocale());
            if(method_exists($translation, 'getImage')) {
                $image = $translation->getImage();
                if ($image !== null) {
                    $provider = $this->container->get($image->getProviderName());
                    $seoSerivice->addMeta('property', 'og:image', $request->getScheme() . '://' . $request->getHost() . $provider->generatePublicUrl($image, 'reference'));
                } else {
                    $seoSerivice->addMeta('property', 'og:image', $request->getScheme() . '://' . $request->getHost() . '/images/facebook-no-img.jpg');
                }
            } elseif(method_exists($translation, 'getGallery')) {
                $gallery = $translation->getGallery();
                if ($gallery != null) {
                    foreach ($gallery->getGalleryHasMedias() as $row) {
                        if ($row !== null) {
                            $media = $row->getMedia();
                            $provider = $this->container->get($media->getProviderName());
                            $seoSerivice->addMeta('property', 'og:image', $request->getScheme() . '://' . $request->getHost() . $provider->generatePublicUrl($media, 'reference'));
                            break;
                        }
                    }
                    $seoSerivice->addMeta('property', 'og:image', $request->getScheme() . '://' . $request->getHost() . '/images/facebook-no-img.jpg');
                } else {
                    $seoSerivice->addMeta('property', 'og:image', $request->getScheme() . '://' . $request->getHost() . '/images/facebook-no-img.jpg');
                }
            } else {
                $seoSerivice->addMeta('property', 'og:image', $request->getScheme() . '://' . $request->getHost() . '/images/facebook-no-img.jpg');
            }
        }else{
            $seoSerivice->addMeta('property', 'og:image', $params['image_url']);
            unset($params['image_url']);
        }

        //other og tags
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $seoSerivice->addMeta('property', $key, $value);
            }
        }
    }
}