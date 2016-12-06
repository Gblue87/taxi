<?php
namespace NewVision\CoreBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;

class SoftDeletableListener
{
	private $container;

	public function __construct($container)
	{
		$this->container = $container;
	}

    private $findForeignKeysSql = '
		SELECT *
		FROM
		  KEY_COLUMN_USAGE
		WHERE
		  REFERENCED_TABLE_NAME = :tableName
		  AND REFERENCED_COLUMN_NAME = :referenceColumnName
		  AND TABLE_SCHEMA = :schema;
    ';

    private $updateSql = 'UPDATE SET :columnName = null WHERE :columnName = :objectId';

    public function preSoftDelete(LifecycleEventArgs $args)
    {
    }

    public function postSoftDelete(LifecycleEventArgs $args)
    {
    	$entity = $args->getEntity();
    	$em = $args->getEntityManager();
    	$meta = $em->getClassMetadata(get_class($entity));
    	$tableName = $meta->getTableName();
    	$referenceColumnName =$meta->getSingleIdentifierFieldName();

    	$iConn = new \PDO('mysql:host=localhost;dbname=information_schema', $this->container->getParameter('database_user'), $this->container->getParameter('database_password'));

    	$st = $iConn->prepare($this->findForeignKeysSql);
    	$st->execute(array(
    		'tableName' => $tableName,
    		'referenceColumnName' => $referenceColumnName,
    		'schema' => $this->container->getParameter('database_name')
    	));
    	$res = $st->fetchAll();

    	$conn = new \PDO('mysql:host=localhost;dbname='.$this->container->getParameter('database_name'), $this->container->getParameter('database_user'), $this->container->getParameter('database_password'));
        $refTables = array();
        foreach ($res as $itm) {
            $refTable = $itm['TABLE_NAME'];
            $colName = $itm['COLUMN_NAME'];
            $id = $entity->getId();
            if($refTable != $tableName && $refTable != $tableName.'_i18n') {
            	$st = $conn->prepare("UPDATE $refTable SET $colName = NULL WHERE $colName = $id");
            	$st->execute();
            	$st->fetch();
            }
        }
    }
}