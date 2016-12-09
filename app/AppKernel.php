<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),

            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),
            new Sonata\MediaBundle\SonataMediaBundle(),
            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
            new Sonata\IntlBundle\SonataIntlBundle(),
            new Sonata\UserBundle\SonataUserBundle(),
            new Sonata\SeoBundle\SonataSeoBundle(),

            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new A2lix\TranslationFormBundle\A2lixTranslationFormBundle(),

            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new DS\ReCaptchaBundle\ReCaptchaBundle(),
            new Application\Sonata\UserBundle\ApplicationSonataUserBundle(),
            new Application\Sonata\MediaBundle\ApplicationSonataMediaBundle(),

            new NewVision\SettingsBundle\NewVisionSettingsBundle(),
            new NewVision\TranslationsBundle\NewVisionTranslationsBundle(),
            new NewVision\PublishWorkflowBundle\NewVisionPublishWorkflowBundle(),
            new NewVision\SEOBundle\NewVisionSEOBundle(),
            new NewVision\TinyMCEBundle\NewVisionTinyMCEBundle(),
            //new NewVision\NotificationsBundle\NewVisionNotificationsBundle(),
            //new NewVision\SearchBundle\NewVisionSearchBundle(),
            new NewVision\ContactsBundle\NewVisionContactsBundle(),
            new NewVision\MenuBundle\NewVisionMenuBundle(),
            //new NewVision\NewsletterBundle\NewVisionNewsletterBundle(),
            //new NewVision\BannersBundle\NewVisionBannersBundle(),
            new NewVision\FrontendBundle\NewVisionFrontendBundle(),
            //new NewVision\MailChimpBundle\NewVisionMailChimpBundle(),
            new NewVision\ContentBundle\NewVisionContentBundle(),
            new NewVision\ServicesBundle\NewVisionServicesBundle(),
            new NewVision\CustomBlocksBundle\NewVisionCustomBlocksBundle(),
            //new NewVision\AccentsBundle\NewVisionAccentsBundle(),
            new NewVision\CoreBundle\NewVisionCoreBundle(),
            new NewVision\AirportsBundle\NewVisionAirportsBundle()
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
