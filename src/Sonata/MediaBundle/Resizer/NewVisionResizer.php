<?php

namespace Sonata\MediaBundle\Resizer;

use Imagine\Image\ImagineInterface;
use Imagine\Image\Box;
use Gaufrette\File;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Resizer\ResizerInterface;
use Imagine\Image\ImageInterface;
use Imagine\Exception\InvalidArgumentException;
use Sonata\MediaBundle\Metadata\MetadataBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class NewVisionResizer implements ResizerInterface
{
    protected $adapter;
    protected $mode;
    protected $metadata;
    protected $container;

    /**
     * @param ImagineInterface $adapter
     * @param string           $mode
     */
    public function __construct(ImagineInterface $adapter, $mode, MetadataBuilderInterface $metadata, ContainerInterface $container)
    {
        $this->adapter = $adapter;
        $this->mode = $mode;
        $this->metadata = $metadata;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function resize(MediaInterface $media, File $in, File $out, $format, array $settings)
    {
        if (!((isset($settings['width']) && $settings['width']) || (isset($settings['height']) && $settings['height']))) {
            throw new \RuntimeException(sprintf('Width parameter is missing in context "%s" for provider "%s"', $media->getContext(), $media->getProviderName()));
        }

        $originalImage = $this->adapter->load($in->getContent());

        //WATERMARK
        if (isset($settings['watermark']) && $settings['watermark'] === true) {
            $imagine = new \Imagine\Gd\Imagine();
            $orgSize = $originalImage->getSize();
            $rootDir = $this->container->get('kernel')->getRootDir();
            $watermark = $imagine->open($rootDir.'/../web/images/frontend/watermark.png');
            $wSize     = $watermark->getSize();

            //POSITION DEFAULT POSITION IS CENTER
            if ($settings['position'] == 'bottom_right') {
                $point = new \Imagine\Image\Point($orgSize->getWidth() - $wSize->getWidth(), $orgSize->getHeight() - $wSize->getHeight());
            }elseif($settings['position'] == 'center'){
                $point = new \Imagine\Image\Point($orgSize->getWidth()/2 - $wSize->getWidth()/2, $orgSize->getHeight()/2 - $wSize->getHeight()/2);
            }elseif($settings['position'] == 'bottom_left'){
                $point = new \Imagine\Image\Point(0, $orgSize->getHeight() - $wSize->getHeight());
            }

            $originalImage->paste($watermark, $point);
        }
        //END WATERMARK

        $size = $this->getBox($media, $settings);
        $mode      = \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND;

        $resizeimg = $originalImage->thumbnail($size, $mode);
        $sizeR     = $resizeimg->getSize();
        $widthR    = $sizeR->getWidth();
        $heightR   = $sizeR->getHeight();

        $content  = $this->adapter->create($size);

        $startX = $startY = 0;
        if ($widthR < $settings['width']) {
            $startX = ($settings['width'] - $widthR) / 2;
        }
        if ($heightR < $settings['height']) {
            $startY = ($settings['height'] - $heightR) / 2;
        }
        $params = array();
        if ($media->getBinaryContent()->getMimeType() == 'image/jpeg') {
            $params['jpeg_quality'] = $settings['quality'];
        }else{
            $params['quality'] = $settings['quality'];
        }
        $contentStream = $content->paste($resizeimg, new \Imagine\Image\Point($startX, $startY))
            ->get($format, $params);

        $out->setContent($contentStream, $this->metadata->get($media, $out->getName()));
    }

    /**
     * {@inheritdoc}
     */
    public function getBox(MediaInterface $media, array $settings)
    {
        $size = $media->getBox();

        if(isset($settings['maxWidth']) && $settings['maxWidth']) {
            $hasWidth = true;
            $hasHeight = false;
        } else if(isset($settings['maxHeight']) && $settings['maxHeight']) {
            $hasWidth = false;
            $hasHeight = true;
        } else {
            $hasWidth = isset($settings['width']) && $settings['width'];
            $hasHeight = isset($settings['height']) && $settings['height'];
        }

        if (!$hasWidth && !$hasHeight) {
            throw new \RuntimeException(sprintf('Width/Height parameter is missing in context "%s" for provider "%s". Please add at least one parameter.', $media->getContext(), $media->getProviderName()));
        }

        if ($hasWidth && $hasHeight) {
            return new Box($settings['width'], $settings['height']);
        }

        if (!$hasHeight) {
            $settings['height'] = intval($settings['width'] * $size->getHeight() / $size->getWidth());
        }

        if (!$hasWidth) {
            $settings['width'] = intval($settings['height'] * $size->getWidth() / $size->getHeight());
        }

        return $this->computeBox($media, $settings);
    }

    /**
     * @throws InvalidArgumentException
     *
     * @param MediaInterface $media
     * @param array          $settings
     *
     * @return Box
     */
    private function computeBox(MediaInterface $media, array $settings)
    {
        if ($this->mode !== ImageInterface::THUMBNAIL_INSET && $this->mode !== ImageInterface::THUMBNAIL_OUTBOUND) {
            throw new InvalidArgumentException('Invalid mode specified');
        }

        $size = $media->getBox();

        $ratios = [
            $settings['width'] / $size->getWidth(),
            $settings['height'] / $size->getHeight(),
        ];

        if ($this->mode === ImageInterface::THUMBNAIL_INSET) {
            $ratio = min($ratios);
        } else {
            $ratio = max($ratios);
        }

        return $size->scale($ratio);
    }
}
