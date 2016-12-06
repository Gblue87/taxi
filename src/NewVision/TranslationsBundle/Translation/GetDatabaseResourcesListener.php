<?php

namespace NewVision\TranslationsBundle\Translation;

use NewVision\TranslationsBundle\Storage\StorageInterface;

/**
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class GetDatabaseResourcesListener
{
    /**
     * @var \NewVision\TranslationsBundle\Storage\StorageInterface
     */
    private $storage;

    /**
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Query the database to get translation resources and set it on the event.
     *
     * @param GetDatabaseResourcesEvent $event
     */
    public function onGetDatabaseResources(GetDatabaseResourcesEvent $event)
    {
        // prevent errors on command such as cache:clear if doctrine schema has not been updated yet
        if (!$this->storage->translationsTablesExist()) {
            $resources = array();
        } else {
            $resources = $this->storage->getTransUnitDomainsByLocale();
        }

        $event->setResources($resources);
    }
}
