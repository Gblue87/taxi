<?php

/*
 * This file is part of the NewVision package.
 *
 * (c) Georgi Gyurov && Nikolay Tumbalev <georgi@newvision.bg, n.tumbalev@newvision.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NewVision\CoreBundle\Composer;

use Symfony\Component\ClassLoader\ClassCollectionLoader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;
use Composer\Script\CommandEvent;
use Symfony\Component\Finder\Finder;

/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class ScriptHandler
{
    /**
     * Composer variables are declared static so that an event could update
     * a composer.json and set new options, making them immediately available
     * to forthcoming listeners.
     */
    protected static $options = array(
        'symfony-app-dir' => 'app',
        'symfony-web-dir' => 'web',
        'symfony-assets-install' => 'hard',
        'symfony-cache-warmup' => false,
        'site_name' => ''
    );

    public static function installBundles(CommandEvent $event)
    {
        $rootDir = getcwd();
        $srcDir = $rootDir.'/src/NewVision';
        $vendor = 'NewVision';
        $skeletonDir = $rootDir.'/vendor/newvision-cms/skeleton/NewVisionCMSSkeleton/';
        $options = static::getOptions($event);
        $appDir = $options['symfony-app-dir'];
        $consoleDir = static::getConsoleDir($event);
        $fs = new Filesystem();

        if (!is_dir($skeletonDir)) {
            return;
        }
        $finder = new Finder();
        $finder->directories()->in($skeletonDir)->depth('<1')->notName('*.json');

        foreach ($finder as $dir) {
            $fileName = $dir->getFileName();
            // if (file_exists($srcDir.'/'.$fileName)) {
            //     continue;
            // }
            if (!$event->getIO()->askConfirmation('Искате ли да инсталирате '.$vendor.$fileName.'? [Y/n] ', true)) {
                continue;
            }else{
                $event->getIO()->write('Инсталиране на '.$fileName.'...');
                $kernelFile = $appDir.'/AppKernel.php';
                if (!file_exists($srcDir)) {
                    $fs->mkdir(array($srcDir));
                    $fs->chmod($srcDir, 0755);
                }

                $fs->mkdir(array($srcDir.'/'.$fileName));
                $fs->chmod($srcDir.'/'.$fileName, 0755);

                $fs->mirror($skeletonDir.$fileName, $srcDir.'/'.$fileName, null, array('override' => true));

                $ref = 'new NewVision\CoreBundle\NewVisionCoreBundle(),';
                $bundleDeclaration = "new NewVision\\$fileName\\$vendor$fileName(),";
                $content = file_get_contents($kernelFile);

                if (false === strpos($content, $bundleDeclaration)) {
                    $updatedContent = str_replace($ref, $bundleDeclaration."\n            ".$ref, $content);
                    if ($content === $updatedContent) {
                        throw new \RuntimeException('Unable to patch %s.', $kernelFile);
                    }
                    $fs->dumpFile($kernelFile, $updatedContent);
                }
                if ($fileName != 'ContentBundle') {
                    static::patchBundleConfiguration($appDir, $fs, $vendor, $fileName);
                }
            }
        }

        if (null === $consoleDir) {
            return;
        }
    }

    public static function init(CommandEvent $event)
    {
        $rootDir = getcwd();
        $srcDir = $rootDir.'/src/NewVision';
        $vendor = 'NewVision';
        $skeletonDir = $rootDir.'/vendor/newvision-cms/skeleton/NewVisionCMSSkeleton/';
        $options = static::getOptions($event);
        $appDir = $options['symfony-app-dir'];
        $configFile = $appDir.'/config/config.yml';
        $parametersFile = $appDir.'/config/parameters.yml';
        $cur_dir = explode('/', getcwd());
        $fs = new Filesystem();

        if (file_exists($parametersFile)) {
            return;
        }

        while (!isset($name)) {
            $name = $event->getIO()->askAndValidate('Какво ще бъде името на сайта ?', function($name) {
                if ($name !== '') {
                    return $name;
                }
                throw new \Exception("Err");
            });
        }
        self::$options['site_name'] = $name;
        if ($name != $cur_dir[count($cur_dir)-1]) {

            $event->getIO()->write('Името на сайта е сменено на '.$name.'...');
            $rootDir = getcwd();
            $fs->remove($rootDir.'/.git');
            $event->getIO()->write('Изтрита е гит папката...');
            $output = shell_exec('git init');
            $output = shell_exec('git remote add origin git@dev.newvisiongroup.com:symfony/'.$name.'.git');
            $output = shell_exec('git add .');
            $output = shell_exec('git commit -m "initial commit"');
            $output = shell_exec('git push origin master');
            $output = shell_exec('git checkout -b develop');
            $output = shell_exec('git push origin develop');

            while (!isset($seoName)) {
                $seoName = $event->getIO()->askAndValidate('Адрес за sео ? (neshtosi.com)', function($seoName){
                    if ($seoName !== '') {
                        return $seoName;
                    }
                    throw new \Exception("Err");
                });
            }
            $event->getIO()->write('Добавяне на sео в конфига');
            $configUpdate = file_get_contents($configFile).<<<EOF

sonata_seo:
    page:
        title: '$name'
        metas:
            property:
                # Open Graph information
                # see http://developers.facebook.com/docs/opengraphprotocol/#types or http://ogp.me/
                'og:title':     $name
                'og:site_name':       $name
                'og:description':     $name описание
                'og:image':     http://$seoName/images/logo.png
                'og:url':     http://$seoName
                'og:type':     website
    sitemap:
        doctrine_orm:
            #DELETE THIS IF NEWS CATEGORIES
            news:
                connection: doctrine.dbal.default_connection
                route: post_without_category
                parameters:
                    slug: null
                    _locale: bg
                query: "SELECT ni18n.locale as `_locale`, ni18n.slug as `slug`, n.`updated_at` AS `lastmod`, 'weekly' AS `changefreq`, '0.8' AS `priority` FROM `news` AS n LEFT JOIN `news_i18n` as ni18n on n.id = ni18n.object_id LEFT JOIN publish_workflow as `pw` on n.publishWorkflow_id = pw.id WHERE pw.is_active = 1 AND ((pw.from_date IS NULL AND pw.to_date IS NULL) OR (pw.from_date <= NOW() AND pw.to_date >= NOW())  OR (pw.from_date IS NOT NULL AND pw.from_date <= NOW()) OR (pw.to_date IS NOT NULL AND pw.to_date >= NOW()))"
            #--------------------------------------
            news_category:
                connection: doctrine.dbal.default_connection
                route: posts_categories_category_view
                parameters:
                    categorySlug: null
                    _locale: bg
                query: "SELECT ni18n.locale as `_locale`, ni18n.slug as `categorySlug`, n.`updated_at` AS `lastmod`, 'weekly' AS `changefreq`, '0.8' AS `priority` FROM `news_categories` AS n LEFT JOIN `news_categories_i18n` as ni18n on n.id = ni18n.object_id LEFT JOIN publish_workflow as `pw` on n.publishWorkflow_id = pw.id WHERE pw.is_active = 1 AND ((pw.from_date IS NULL AND pw.to_date IS NULL) OR (pw.from_date <= NOW() AND pw.to_date >= NOW())  OR (pw.from_date IS NOT NULL AND pw.from_date <= NOW()) OR (pw.to_date IS NOT NULL AND pw.to_date >= NOW()))"

            news_listing:
                connection: doctrine.dbal.default_connection
                route: posts_list
                parameters:
                    _locale: bg
                query: "SELECT updated_at as lastmod, 'weekly' as changefreq, '0.5' as priority FROM news LIMIT 1"
            #DELETE THIS IF NO NEWS CATEGORIES
            news_view_with_category:
                connection: doctrine.dbal.default_connection
                route: posts_category_post_view
                parameters:
                    categorySlug: null
                    slug: null
                    _locale: bg
                query: "SELECT categorySlug, slug, `_locale`, lastmod AS `lastmod`, 'weekly' AS `changefreq`, '0.8' AS `priority`  FROM (SELECT ni18n.locale as `_locale`, ni18n.slug as `slug`,c.newscategory_id as `categoryId`, pc.id as `pcid`, pci18n.slug as `categorySlug`, n.`updated_at` AS `lastmod`, 'weekly' AS `changefreq`, '0.8' AS `priority`FROM news AS n LEFT JOIN news_i18n as ni18n ON n.id = ni18n.object_id LEFT JOIN publish_workflow as pw on n.publishWorkflow_id = pw.id LEFT JOIN news_categories_m2m as c ON n.id = c.news_id LEFT JOIN news_categories as pc ON pc.id = c.newscategory_id LEFT JOIN news_categories_i18n as pci18n ON pc.id = pci18n.object_id LEFT JOIN publish_workflow as pwCat on pc.publishWorkflow_id = pwCat.id WHERE (pwCat.is_active = 1 AND ((pwCat.from_date IS NULL AND pwCat.to_date IS NULL) OR (pwCat.from_date <= NOW() AND pwCat.to_date >= NOW())) OR (pwCat.from_date IS NOT NULL AND pwCat.from_date <= NOW()) OR (pwCat.to_date IS NOT NULL AND pwCat.to_date >= NOW())) AND (pw.is_active = 1 AND ((pw.from_date IS NULL AND pw.to_date IS NULL) OR (pw.from_date <= NOW() AND pw.to_date >= NOW()) OR (pw.from_date IS NOT NULL AND pw.from_date <= NOW()) OR (pw.to_date IS NOT NULL AND pw.to_date >= NOW())))) as catinfo WHERE catinfo.pcid = catinfo.categoryId"
                #--------------------------------------
            #DELETE THIS IF NO PRODUCTS BUNDLE
            products_listing:
                connection: doctrine.dbal.default_connection
                route: products_list
                parameters:
                    categorySlug: null
                    _locale: bg
                query: "SELECT updated_at as lastmod, 'weekly' as changefreq, '0.5' as priority FROM products LIMIT 1"

            product_category:
                connection: doctrine.dbal.default_connection
                route: products_categories_category_view
                parameters:
                    slug: null
                    _locale: bg
                query: "SELECT ni18n.locale as `_locale`, ni18n.slug as `slug`, n.`updated_at` AS `lastmod`, 'weekly' AS `changefreq`, '0.8' AS `priority` FROM `product_categories` AS n LEFT JOIN `product_categories_i18n` as ni18n on n.id = ni18n.object_id LEFT JOIN publish_workflow as `pw` on n.publishWorkflow_id = pw.id WHERE pw.is_active = 1 AND ((pw.from_date IS NULL AND pw.to_date IS NULL) OR (pw.from_date <= NOW() AND pw.to_date >= NOW())  OR (pw.from_date IS NOT NULL AND pw.from_date <= NOW()) OR (pw.to_date IS NOT NULL AND pw.to_date >= NOW()))"

            product_view:
                connection: doctrine.dbal.default_connection
                route: products_category_product_view
                parameters:
                    categorySlug: null
                    slug: null
                    _locale: bg
                query: "SELECT categorySlug, slug, `_locale`, lastmod AS `lastmod`, 'weekly' AS `changefreq`, '0.8' AS `priority`  FROM (SELECT ni18n.locale as `_locale`, ni18n.slug as `slug`,c.productcategory_id as `categoryId`, pc.id as `pcid`, pci18n.slug as `categorySlug`, n.`updated_at` AS `lastmod`, 'weekly' AS `changefreq`, '0.8' AS `priority`FROM products AS n LEFT JOIN products_i18n as ni18n ON n.id = ni18n.object_id LEFT JOIN publish_workflow as pw on n.publishWorkflow_id = pw.id LEFT JOIN products_categories as c ON n.id = c.product_id LEFT JOIN product_categories as pc ON pc.id = c.productcategory_id LEFT JOIN product_categories_i18n as pci18n ON pc.id = pci18n.object_id LEFT JOIN publish_workflow as pwCat on pc.publishWorkflow_id = pwCat.id WHERE (pwCat.is_active = 1 AND ((pwCat.from_date IS NULL AND pwCat.to_date IS NULL) OR (pwCat.from_date <= NOW() AND pwCat.to_date >= NOW())) OR (pwCat.from_date IS NOT NULL AND pwCat.from_date <= NOW()) OR (pwCat.to_date IS NOT NULL AND pwCat.to_date >= NOW())) AND (pw.is_active = 1 AND ((pw.from_date IS NULL AND pw.to_date IS NULL) OR (pw.from_date <= NOW() AND pw.to_date >= NOW()) OR (pw.from_date IS NOT NULL AND pw.from_date <= NOW()) OR (pw.to_date IS NOT NULL AND pw.to_date >= NOW())))) as catinfo WHERE catinfo.pcid = catinfo.categoryId"
            #--------------------------------------
            #DELETE THIS IF NO SERVICES BUNDLE
            service_listing:
                connection: doctrine.dbal.default_connection
                route: services_list
                parameters:
                    _locale: bg
                query: "SELECT updated_at as lastmod, 'weekly' as changefreq, '0.5' as priority FROM services LIMIT 1"

            service_category:
                connection: doctrine.dbal.default_connection
                route: service_without_category
                parameters:
                    slug: null
                    _locale: bg
                query: "SELECT ni18n.locale as `_locale`, ni18n.slug as `slug`, n.`updated_at` AS `lastmod`, 'weekly' AS `changefreq`, '0.8' AS `priority` FROM `service_categories` AS n LEFT JOIN `service_categories_i18n` as ni18n on n.id = ni18n.object_id LEFT JOIN publish_workflow as `pw` on n.publishWorkflow_id = pw.id WHERE pw.is_active = 1 AND ((pw.from_date IS NULL AND pw.to_date IS NULL) OR (pw.from_date <= NOW() AND pw.to_date >= NOW()) OR (pw.from_date IS NOT NULL AND pw.from_date <= NOW()) OR (pw.to_date IS NOT NULL AND pw.to_date >= NOW()))"

            service_view:
                connection: doctrine.dbal.default_connection
                route: services_category_service_view
                parameters:
                    categorySlug: null
                    slug: null
                    _locale: bg
                query: "SELECT categorySlug, slug, `_locale`, lastmod AS `lastmod`, 'weekly' AS `changefreq`, '0.8' AS `priority`  FROM (SELECT ni18n.locale as `_locale`, ni18n.slug as `slug`,c.servicecategory_id as `categoryId`, pc.id as `pcid`, pci18n.slug as `categorySlug`, n.`updated_at` AS `lastmod`, 'weekly' AS `changefreq`, '0.8' AS `priority`FROM services AS n LEFT JOIN services_i18n as ni18n ON n.id = ni18n.object_id LEFT JOIN publish_workflow as pw on n.publishWorkflow_id = pw.id LEFT JOIN services_categories as c ON n.id = c.service_id LEFT JOIN service_categories as pc ON pc.id = c.servicecategory_id LEFT JOIN service_categories_i18n as pci18n ON pc.id = pci18n.object_id LEFT JOIN publish_workflow as pwCat on pc.publishWorkflow_id = pwCat.id WHERE (pwCat.is_active = 1 AND ((pwCat.from_date IS NULL AND pwCat.to_date IS NULL) OR (pwCat.from_date <= NOW() AND pwCat.to_date >= NOW())) OR (pwCat.from_date IS NOT NULL AND pwCat.from_date <= NOW()) OR (pwCat.to_date IS NOT NULL AND pwCat.to_date >= NOW())) AND (pw.is_active = 1 AND ((pw.from_date IS NULL AND pw.to_date IS NULL) OR (pw.from_date <= NOW() AND pw.to_date >= NOW()) OR (pw.from_date IS NOT NULL AND pw.from_date <= NOW()) OR (pw.to_date IS NOT NULL AND pw.to_date >= NOW())))) as catinfo WHERE catinfo.pcid = catinfo.categoryId"
            #--------------------------------------
            #DELETE THIS IF NO GALLERIES BUNDLE
            gallery:
                connection: doctrine.dbal.default_connection
                route: gallery_view
                parameters:
                    slug: null
                    _locale: bg
                query: "SELECT ni18n.locale as `_locale`, ni18n.slug as `slug`, n.`updated_at` AS `lastmod`, 'weekly' AS `changefreq`, '0.8' AS `priority` FROM `galleries` AS n LEFT JOIN `galleries_i18n` as ni18n on n.id = ni18n.object_id LEFT JOIN publish_workflow as `pw` on n.publishWorkflow_id = pw.id WHERE pw.is_active = 1 AND ((pw.from_date IS NULL AND pw.to_date IS NULL) OR (pw.from_date <= NOW() AND pw.to_date >= NOW())  OR (pw.from_date IS NOT NULL AND pw.from_date <= NOW()) OR (pw.to_date IS NOT NULL AND pw.to_date >= NOW()))"
            #--------------------------------------
            galleries_listing:
                connection: doctrine.dbal.default_connection
                route: galleries
                parameters:
                    slug: null
                    _locale: bg
                query: "SELECT updated_at as lastmod, 'weekly' as changefreq, '0.5' as priority FROM galleries LIMIT 1"
            #DELETE THIS IF NO DISTRIBUTORS BUNDLE
            distributors:
                connection: doctrine.dbal.default_connection
                route: distributors
                parameters:
                    _locale: bg
                query: "SELECT updated_at as lastmod, 'weekly' as changefreq, '0.5' as priority FROM dealer LIMIT 1"
            #--------------------------------------
            #DELETE THIS IF NO CAREERS BUNDLE
            careers:
                connection: doctrine.dbal.default_connection
                route: careers
                parameters:
                    _locale: bg
                query: "SELECT updatedAt as lastmod, 'weekly' as changefreq, '0.5' as priority FROM careers LIMIT 1"

            career_view:
                connection: doctrine.dbal.default_connection
                route: career_view
                parameters:
                    slug: null
                    _locale: bg
                query: "SELECT ni18n.locale as `_locale`, ni18n.slug as `slug`, n.`updatedAt` AS `lastmod`, 'weekly' AS `changefreq`, '0.8' AS `priority` FROM `careers` AS n LEFT JOIN `careers_i18n` as ni18n on n.id = ni18n.object_id LEFT JOIN publish_workflow as `pw` on n.publishWorkflow_id = pw.id WHERE pw.is_active = 1 AND ((pw.from_date IS NULL AND pw.to_date IS NULL) OR (pw.from_date <= NOW() AND pw.to_date >= NOW())  OR (pw.from_date IS NOT NULL AND pw.from_date <= NOW()) OR (pw.to_date IS NOT NULL AND pw.to_date >= NOW()))"
            #--------------------------------------
            contacts:
                connection: doctrine.dbal.default_connection
                route: contacts
                parameters:
                    _locale: bg
                query: "SELECT updated_at as lastmod, 'weekly' as changefreq, '0.5' as priority FROM content LIMIT 1"

            #DELETE THIS IF NO REFERENTIONS BUNDLE
            referentions:
                connection: doctrine.dbal.default_connection
                route: referentions
                parameters:
                    _locale: bg
                query: "SELECT updated_at as lastmod, 'weekly' as changefreq, '0.5' as priority FROM referentions LIMIT 1"
            #--------------------------------------
            #DELETE THIS IF NO BRANDS BUNDLE
            brands_list:
                connection: doctrine.dbal.default_connection
                route: brands_list
                parameters:
                    _locale: bg
                query: "SELECT updated_at as lastmod, 'weekly' as changefreq, '0.5' as priority FROM brands LIMIT 1"
            brands_brand_view:
                connection: doctrine.dbal.default_connection
                route: brands_brand_view
                parameters:
                    slug: null
                    _locale: bg
                query: "SELECT ni18n.locale as `_locale`, ni18n.slug as `slug`, n.`updatedAt` AS `lastmod`, 'weekly' AS `changefreq`, '0.8' AS `priority` FROM `brands` AS n LEFT JOIN `brands_i18n` as ni18n on n.id = ni18n.object_id LEFT JOIN publish_workflow as `pw` on n.publishWorkflow_id = pw.id WHERE pw.is_active = 1 AND ((pw.from_date IS NULL AND pw.to_date IS NULL) OR (pw.from_date <= NOW() AND pw.to_date >= NOW())  OR (pw.from_date IS NOT NULL AND pw.from_date <= NOW()) OR (pw.to_date IS NOT NULL AND pw.to_date >= NOW()))"
            #--------------------------------------
            images:
                types: [image]
                route: sonata_media_view
                connection: doctrine.dbal.default_connection
                parameters:
                    id: null
                query: "SELECT id, updated_at as lastmod, 'weekly' as changefreq, '0.5' as priority FROM media__media"

newvision_tiny_mce:
    table_class_list: [ {title: 'Таблица с основни стилове', value: 'tableMain'} ]
    link_class_list: [ {title: 'Бутон със стилове', value: 'btnBlue'} ]
EOF;

            $fs->dumpFile($configFile, $configUpdate);
            $event->getIO()->write('Може да използвате вашият сайт :)');

        }

    }

    public static function changeDirName(CommandEvent $event)
    {
        $fs = new Filesystem();
        $rootDir = getcwd();
        $options = static::getOptions($event);
        $cur_dir = explode('/', $rootDir);
        if ($options['site_name'] == '') {
            self::$options['site_name'] = $cur_dir[count($cur_dir)-1];
        }
        if (self::$options['site_name'] != $cur_dir[count($cur_dir)-1]) {
            $fs->rename($rootDir, dirname($rootDir).'/'.$options['site_name']);
            $event->getIO()->write('Главната директория е променена на '.self::$options['site_name']);
        }
        $rootDir = getcwd();
        $fs->chmod($rootDir.'/app/logs', 0777, 0000, true);
        $fs->chmod($rootDir.'/app/cache', 0777, 0000, true);
        $fs->chmod($rootDir.'/web', 0777, 0000, true);
        $fs->mkdir($rootDir.'/web/uploads/assets');
        $fs->mkdir($rootDir.'/web/uploads/career-applyment');
        shell_exec('sudo sh '.$rootDir.'/vendor/newvision/core-bundle/NewVision/CoreBundle/Composer/vhost.sh');
    }

    private static function patchBundleConfiguration($appDir, Filesystem $fs, $vendor, $fileName)
    {
        if ($fileName == 'TinyMCEBundle' || $fileName == 'MenuBundle') {
            return;
        }
        $routingFile = $appDir.'/config/routing.yml';
        $chunks = preg_split('/(?=[A-Z])/', $fileName);
        $first = strtolower($chunks[1]);
        $second = strtolower($chunks[2]);
        $routeName = $first.'_'.$second;
        $eof = <<<EOF

# $vendor$fileName routes
$routeName:
    resource: "@$vendor$fileName/Controller"
    type:     annotation
    prefix:   /{_locale}
    requirements:
        _locale: "[a-z]{2}"

EOF;
        $routingData = $eof.file_get_contents($routingFile);
        $fs->dumpFile($routingFile, $routingData);
    }


    protected static function getOptions(CommandEvent $event)
    {
        $options = array_merge(static::$options, $event->getComposer()->getPackage()->getExtra());

        $options['symfony-assets-install'] = getenv('SYMFONY_ASSETS_INSTALL') ?: $options['symfony-assets-install'];

        $options['process-timeout'] = $event->getComposer()->getConfig()->get('process-timeout');

        return $options;
    }

    protected static function executeCommand(CommandEvent $event, $consoleDir, $cmd, $timeout = 300)
    {
        $php = escapeshellarg(static::getPhp(false));
        $phpArgs = implode(' ', array_map('escapeshellarg', static::getPhpArguments()));
        $console = escapeshellarg($consoleDir.'/console');
        if ($event->getIO()->isDecorated()) {
            $console .= ' --ansi';
        }

        $process = new Process($php.($phpArgs ? ' '.$phpArgs : '').' '.$console.' '.$cmd, null, null, null, $timeout);
        $process->run(function ($type, $buffer) use ($event) { $event->getIO()->write($buffer, false); });
        if (!$process->isSuccessful()) {
            throw new \RuntimeException(sprintf("An error occurred when executing the \"%s\" command:\n\n%s\n\n%s.", escapeshellarg($cmd), $process->getOutput(), $process->getErrorOutput()));
        }
    }

    protected static function getPhp($includeArgs = true)
    {
        $phpFinder = new PhpExecutableFinder();
        if (!$phpPath = $phpFinder->find($includeArgs)) {
            throw new \RuntimeException('The php executable could not be found, add it to your PATH environment variable and try again');
        }

        return $phpPath;
    }

    protected static function getPhpArguments()
    {
        $arguments = array();

        $phpFinder = new PhpExecutableFinder();
        if (method_exists($phpFinder, 'findArguments')) {
            $arguments = $phpFinder->findArguments();
        }

        if (false !== $ini = php_ini_loaded_file()) {
            $arguments[] = '--php-ini='.$ini;
        }

        return $arguments;
    }

        /**
     * Returns a relative path to the directory that contains the `console` command.
     *
     * @param CommandEvent $event      The command event.
     * @param string       $actionName The name of the action
     *
     * @return string|null The path to the console directory, null if not found.
     */
    protected static function getConsoleDir(CommandEvent $event)
    {
        $options = static::getOptions($event);

        if (static::useNewDirectoryStructure($options)) {
            if (!static::hasDirectory($event, 'symfony-bin-dir', $options['symfony-bin-dir'])) {
                return;
            }

            return $options['symfony-bin-dir'];
        }

        if (!static::hasDirectory($event, 'symfony-app-dir', $options['symfony-app-dir'], 'execute command')) {
            return;
        }

        return $options['symfony-app-dir'];
    }

    /**
     * Returns true if the new directory structure is used.
     *
     * @param array $options Composer options
     *
     * @return bool
     */
    protected static function useNewDirectoryStructure(array $options)
    {
        return isset($options['symfony-var-dir']) && is_dir($options['symfony-var-dir']);
    }

    protected static function hasDirectory(CommandEvent $event, $configName, $path)
    {
        if (!is_dir($path)) {
            $event->getIO()->write(sprintf('The %s (%s) specified in composer.json was not found in %s, can not %s.', $configName, $path, getcwd(), $actionName));

            return false;
        }

        return true;
    }

    public static function createDB(CommandEvent $event)
    {
        $consoleDir = static::getConsoleDir($event);
        $rootDir = getcwd();
        $options = static::getOptions($event);
        $name = $options['site_name'];
        $event->getIO()->write('Създаване на датабаза demo_'.$name);
        $output = @shell_exec('mysql -u demo -pnewvisiondemo -e "create database demo_'.$name.' character set utf8 collate utf8_unicode_ci;"');
        $output = shell_exec('exit;');

        static::executeCommand($event, $consoleDir, 'doctrine:schema:update --force', $options['process-timeout']);

        $cur_dir = explode('/', $rootDir);
        if ($cur_dir[count($cur_dir)-1] == 'newvision-cms') {
            static::executeCommand($event, $consoleDir, 'doctrine:fixtures:load --append', $options['process-timeout']);
        }
    }

    public static function fixBootstrapCache(CommandEvent $event)
    {
        $str=file_get_contents('vendor/composer/autoload_real.php');
        $str=str_replace("require \$file;", "if(!preg_match('/block-bundle/', \$file)){require \$file;}",$str);
        file_put_contents('vendor/composer/autoload_real.php', $str);

        $str = file_get_contents('vendor/newvision/core-bundle/NewVision/CoreBundle/DataFixtures/ORM/GedmoFix.php');
        file_put_contents('vendor/gedmo/doctrine-extensions/lib/Gedmo/Tree/Mapping/Driver/Xml.php', $str);
    }
}
