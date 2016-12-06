<?php
namespace NewVision\FrontendBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Application\Sonata\UserBundle\Entity\User;
use NewVision\SettingsBundle\Entity\Setting;
use NewVision\ContentBundle\Entity\Content;
use NewVision\PublishWorkflowBundle\Entity\PublishWorkflow;
use NewVision\ContentBundle\Entity\ContentTranslation;

class LoadData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
    private $manager;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $this->container->get('doctrine')->getManager();
        $evm = $this->manager->getEventManager();

        $listener = $this->container->get('gedmo.listener.tree');
        $evm->removeEventSubscriber($listener);

        $userAdmin = new User();
        $hash = $this->container->get('security.password_encoder')->encodePassword($userAdmin, 'vi_är_inte_bönder');
        $userAdmin->setUsername('newvision');
        $userAdmin->setEmail('georgi@newvision.bg');
        $userAdmin->setPassword($hash);
        $userAdmin->setEnabled(true);
        $userAdmin->setSuperAdmin(true);

        $fb = new Setting();
        $fb->setLabel('Facebook линк');
        $fb->setName('fb');
        $fb->setValue('http://facebook.com');
        $fb->setVisible(true);

        $twitter = new Setting();
        $twitter->setLabel('Twitter линк');
        $twitter->setName('twitter');
        $twitter->setValue('http://twitter.com');
        $twitter->setVisible(true);

        $gplus = new Setting();
        $gplus->setLabel('Google plus линк');
        $gplus->setName('gplus');
        $gplus->setValue('http://google.com');
        $gplus->setVisible(true);

        $pinterest = new Setting();
        $pinterest->setLabel('Pinterest линк');
        $pinterest->setName('pinterest');
        $pinterest->setValue('http://pinterest.com');
        $pinterest->setVisible(true);

        $linkedin = new Setting();
        $linkedin->setLabel('LinkedIn линк');
        $linkedin->setName('linkedin');
        $linkedin->setValue('http://linkedin.com');
        $linkedin->setVisible(true);

        $instagram = new Setting();
        $instagram->setLabel('Instagram линк');
        $instagram->setName('instagram');
        $instagram->setValue('http://instagram.com');
        $instagram->setVisible(true);

        $youtube = new Setting();
        $youtube->setLabel('Youtube линк');
        $youtube->setName('youtube');
        $youtube->setValue('http://youtube.com');
        $youtube->setVisible(true);

        $ga = new Setting();
        $ga->setLabel('Google analytics код');
        $ga->setName('ga_code');
        $ga->setValue('');
        $ga->setVisible(true);

        $contactsEmail = new Setting();
        $contactsEmail->setLabel('Имейл за контакти');
        $contactsEmail->setName('contact_email');
        $contactsEmail->setValue('tester@newvision.bg');
        $contactsEmail->setVisible(true);

        $careersEmail = new Setting();
        $careersEmail->setLabel('Свободно кандидатстване в кариери');
        $careersEmail->setName('careers_form');
        $careersEmail->setValue(true);
        $careersEmail->setVisible(true);

        $senderEmail = new Setting();
        $senderEmail->setLabel('Имейл на изпращач');
        $senderEmail->setName('sender_email');
        $senderEmail->setValue('tester@newvision.bg');
        $senderEmail->setVisible(true);

        $careersEmail = new Setting();
        $careersEmail->setLabel('Имейл за кариери');
        $careersEmail->setName('careers_email');
        $careersEmail->setValue('tester@newvision.bg');
        $careersEmail->setVisible(true);

        $defaultLocale = $this->container->getParameter('locale');

        $contentArray = array(
            'homepage' => array(
                'title' => array(
                    'bg' => 'Начало',
                    'en' => 'Homepage'
                ),
                'slug' => array(
                    'bg' => 'nachalo',
                    'en' => 'homepage'
                )
            ),
            'news' => array(
                'title' => array(
                    'bg' => 'Новини',
                    'en' => 'News'
                ),
                'slug' => array(
                    'bg' => 'novini',
                    'en' => 'news'
                )
            ),
            'sitemap' => array(
                'title' => array(
                    'bg' => 'Карта на сайта',
                    'en' => 'Sitemap'
                ),
                'slug' => array(
                    'bg' => 'karta-na-saita',
                    'en' => 'sitemap'
                )
            ),
            'contacts' => array(
                'title' => array(
                    'bg' => 'Контакти',
                    'en' => 'Contacts'
                ),
                'slug' => array(
                    'bg' => 'kontakti',
                    'en' => 'contacts'
                )
            ),
            '404' => array(
                'title' => array(
                    'bg' => '404 Страницата не съществува',
                    'en' => '404 Page not found'
                ),
                'slug' => array(
                    'bg' => '404-strnitsata-ne-syshtestvuva',
                    'en' => '404-page-not-found'
                )
            ),
            'careers' => array(
                'title' => array(
                    'bg' => 'Кариери',
                    'en' => 'Careers'
                ),
                'slug' => array(
                    'bg' => 'karieri',
                    'en' => 'careers'
                )
            ),
            'dealers' => array(
                'title' => array(
                    'bg' => 'Дистрибутори',
                    'en' => 'Distributors'
                ),
                'slug' => array(
                    'bg' => 'dilyri',
                    'en' => 'dealers'
                )
            ),
            'products' => array(
                'title' => array(
                    'bg' => 'Продукти',
                    'en' => 'Products'
                ),
                'slug' => array(
                    'bg' => 'produkti',
                    'en' => 'products'
                )
            ),
            'services' => array(
                'title' => array(
                    'bg' => 'Услуги',
                    'en' => 'Services'
                ),
                'slug' => array(
                    'bg' => 'uslugi',
                    'en' => 'services'
                )
            ),
            'search' => array(
                'title' => array(
                    'bg' => 'Резултати от търсене',
                    'en' => 'Results of search'
                ),
                'slug' => array(
                    'bg' => 'rezultati-ot-tyrsene',
                    'en' => 'results-of-search'
                )
            ),
            'about-company' => array(
                'title' => array(
                    'bg' => 'За компанията',
                    'en' => 'About company'
                ),
                'slug' => array(
                    'bg' => 'za-kompaniyata',
                    'en' => 'about-company'
                )
            ),
            'about-us' => array(
                'title' => array(
                    'bg' => 'За нас',
                    'en' => 'About us'
                ),
                'slug' => array(
                    'bg' => 'za-nas',
                    'en' => 'about-us'
                )
            ),
            'our-team' => array(
                'title' => array(
                    'bg' => 'Нашият екип',
                    'en' => 'Our team'
                ),
                'slug' => array(
                    'bg' => 'nashiyat-ekip',
                    'en' => 'our-team'
                )
            ),
            'mission-and-vision' => array(
                'title' => array(
                    'bg' => 'Мисия и визия',
                    'en' => 'Mission and vision'
                ),
                'slug' => array(
                    'bg' => 'misiya-i-viziya',
                    'en' => 'mission-and-vision'
                )
            ),
            'gallery' => array(
                'title' => array(
                    'bg' => 'Галерия',
                    'en' => 'Gallery'
                ),
                'slug' => array(
                    'bg' => 'galeriya',
                    'en' => 'gallery'
                )
            ),
            'partners' => array(
                'title' => array(
                    'bg' => 'Партньори',
                    'en' => 'Partners'
                ),
                'slug' => array(
                    'bg' => 'partnyori',
                    'en' => 'partners'
                )
            ),
            'referentions' => array(
                'title' => array(
                    'bg' => 'Референции',
                    'en' => 'Referentions'
                ),
                'slug' => array(
                    'bg' => 'referentsii',
                    'en' => 'referentions'
                )
            ),
            '500-internal-server-error' => array(
                'title' => array(
                    'bg' => '500 Грешка в сървъра',
                    'en' => '500 Internal server error'
                ),
                'slug' => array(
                    'bg' => '500-greshka-v-syrvyra',
                    'en' => '500-internal-server-error'
                )
            ),
            '403-forbidden' => array(
                'title' => array(
                    'bg' => '403 Нямате права за достъп',
                    'en' => '403 Forbidden'
                ),
                'slug' => array(
                    'bg' => '403-nyamate-prava-za-dostyp',
                    'en' => '403-forbidden'
                )
            ),
            '503-service-unavailable' => array(
                'title' => array(
                    'bg' => '503 Услугата е недостъпна',
                    'en' => '503 Service unavailable'
                ),
                'slug' => array(
                    'bg' => '503-uslugata-e-nedostypna',
                    'en' => '503-service-unavailable'
                )
            ),
            '503-service-unavailable' => array(
                'title' => array(
                    'bg' => '503 Услугата е недостъпна',
                    'en' => '503 Service unavailable'
                ),
                'slug' => array(
                    'bg' => '503-uslugata-e-nedostypna',
                    'en' => '503-service-unavailable'
                )
            ),
            'brands' => array(
                'title' => array(
                    'bg' => 'Марки',
                    'en' => 'Brands'
                ),
                'slug' => array(
                    'bg' => 'marki',
                    'en' => 'brands'
                )
            ),
        );
        $i = 1;
        foreach ($contentArray as $value) {
            $this->createContentObject(
                $value['slug']['bg'],
                $value['slug']['en'],
                $value['title']['bg'],
                $value['title']['en'],
                $i,
                ++$i,
                $defaultLocale
            );
            $i++;
        }

        //user
        $this->manager->persist($userAdmin);
        //settings
        $this->manager->persist($fb);
        $this->manager->persist($twitter);
        $this->manager->persist($gplus);
        $this->manager->persist($pinterest);
        $this->manager->persist($linkedin);
        $this->manager->persist($instagram);
        $this->manager->persist($youtube);
        $this->manager->persist($ga);
        $this->manager->persist($contactsEmail);
        $this->manager->persist($careersEmail);
        $this->manager->persist($senderEmail);
        $this->manager->persist($careersEmail);

        $this->manager->flush();
    }

    private function createContentObject($slugBg, $slugEn, $titleBg, $titleEn, $lft, $rgt, $defaultLocale)
    {
        $pw = new PublishWorkflow();
        $pw->setIsActive(1);
        $pw->setIsHidden(0);
        $pw->setCreatedAt(new \DateTime());
        $pw->setUpdatedAt(new \DateTime());
        $this->manager->persist($pw);

        $content = new Content();
        $content->setSlug($defaultLocale == 'bg' ? $slugBg : $slugEn);
        $content->setTitle($defaultLocale == 'bg' ? $titleBg : $titleEn);
        $content->setIsSystem(1);
        $content->setRoot(1);
        $content->setLft($lft);
        $content->setRgt($rgt);
        $content->setLvl(0);
        $content->setCreatedAt(new \DateTime());
        $content->setUpdatedAt(new \DateTime());
        $content->setPublishWorkflow($pw);
        $translatable = $this->container->get('gedmo.listener.translatable');
        $translatable->setTranslatableLocale($defaultLocale);
        $this->manager->persist($content);
        $this->manager->flush();

    }
}
