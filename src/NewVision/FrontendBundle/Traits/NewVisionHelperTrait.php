<?php
namespace NewVision\FrontendBundle\Traits;

trait NewVisionHelperTrait
{
    private function generateBreadCrumbs($request)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();

        $slug = $request->get('slug');
        $categorySlug = $request->get('categorySlug');

        $itemsRepo = $em->getRepository($this->itemsRepo);
        if (empty($this->itemsCategoriesRepo) == false) {
            $itemsCategoriesRepo = $em->getRepository($this->itemsCategoriesRepo);
        }

        $content = $em->getRepository('NewVisionContentBundle:Content')->findOneById($this->contentPageId);
        if ($content != null) {
            if ($categorySlug != null) {
                $breadCrumbs[$content->getTitle()] = $this->generateUrl($this->mainRootName);

                $itemCategoryObject = $itemsCategoriesRepo->findOneBySlugAndLocale($categorySlug, $locale);
                if ($itemCategoryObject == null) {
                    throw $this->createNotFoundException(sprintf("Category %s not found", $categorySlug));
                }

                $path = $itemsCategoriesRepo->getPath($itemCategoryObject);
                if ($path != null) {
                    foreach ($path as $item) {
                        $breadCrumbs[$item->getTitle()] = $this->generateUrl($item->getRoute(), $item->getRouteParams());
                    }
                }

                if ($slug != '') {
                    $itemObject = $itemsRepo->findOneBySlugAndLocale($slug, $locale);
                    if ($itemObject == null) {
                        throw $this->createNotFoundException(sprintf("Item %s not found", $slug));
                    }

                    if ($itemObject != null) {
                        $breadCrumbs[$itemObject->getTitle()] = null;
                    }
                }
            } elseif($slug != null) {
                $breadCrumbs[$content->getTitle()] = $this->generateUrl($this->mainRootName);

                $itemsObject = $itemsRepo->findOneBySlugAndLocale($slug, $locale);
                if ($itemsObject == null) {
                    throw $this->createNotFoundException(sprintf("Category %s not found", $slug));
                }

                $breadCrumbs[$itemsObject->getTitle()] = null;
            } else {
                $breadCrumbs[$content->getTitle()] = null;
            }
        } else {
            if ($slug != null) {
                $lastNode = $itemsRepo->findOneBySlugAndLocale($slug, $locale);
                if ($lastNode == null) {
                    throw $this->createNotFoundException(sprintf("Item %s not found", $slug));
                }

                $breadCrumbs[$lastNode->getTitle()] = null;
            } else {
                return null;
            }
        }

        return $breadCrumbs;
    }

    private function generateSeo($item)
    {
        $dispatcher = $this->get('event_dispatcher');
        $event = new \NewVision\SEOBundle\Event\SeoEvent($item);
        $dispatcher->dispatch('newvision.seo', $event);
    }

    private function generateOgTags($item, $params = array())
    {
        $this->get('newvision.og_tags')->loadOgTags($item, $params);
    }

    private function generateSeoAndOgTags($item, $params = array())
    {
        $this->generateSeo($item);
        $this->generateOgTags($item, $params);
    }

    private function getImageUrlFromGallery($gallery)
    {
        $params = array();

        if ($gallery && $gallery->getGalleryHasMedias() && $gallery->getEnabled() && count($gallery->getGalleryHasMedias())) {
            $image = null;
            foreach ($gallery->getGalleryHasMedias() as $item) {
                if ($item->getEnabled()) {
                    $image = $item->getMedia();
                    break;
                }
            }

            if ($image != null) {
                $provider = $this->container->get($image->getProviderName());
                $params = array(
                    'image_url' => $this->get('request')->getSchemeAndHttpHost() . $provider->generatePublicUrl($image, 'reference')
                );
            }
        }

        return $params;
    }

    private function getContentPage()
    {
        $em = $this->getDoctrine()->getManager();
        $content = $em->getRepository('NewVisionContentBundle:Content')->findOneById($this->contentPageId);
        if (!$content) {
            throw $this->createNotFoundException(sprintf("Content page id = %s not found", $this->contentPageId));
        }

        return $content;
    }
}
