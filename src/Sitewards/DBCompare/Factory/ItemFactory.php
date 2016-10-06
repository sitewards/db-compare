<?php

namespace Sitewards\DBCompare\Factory;

use Sitewards\DBCompare\Worker\Item\StoreConfigWorker;
use Sitewards\DBCompare\Exception\NoItemWorkerMappingException;
use Doctrine\DBAL\Connection;

class ItemFactory
{
    /**
     * @param int $iItemTypeId
     * @param Connection $oConnection
     * @return StoreConfigWorker
     */
    public static function createById($iItemTypeId, Connection $oConnection)
    {
        if ($iItemTypeId === 0) {
            return new StoreConfigWorker($oConnection);
        }
        throw new NoItemWorkerMappingException($iItemTypeId);
    }
}