<?php

namespace Sitewards\DBCompare\Factory;

use Sitewards\DBCompare\Worker\Item\StoreConfigWorker;
use Sitewards\DBCompare\Exception\NoItemWorkerMappingException;
use Doctrine\DBAL\Connection;

class ItemWorkerFactory
{
    /**
     * @param $sItemTypeId
     * @param Connection $oConnection
     * @return StoreConfigWorker
     * @throws NoItemWorkerMappingException
     */
    public function createById($sItemTypeId, Connection $oConnection)
    {
        if ($sItemTypeId === StoreConfigWorker::S_WORKER_ID) {
            return new StoreConfigWorker($oConnection);
        }
        throw new NoItemWorkerMappingException($sItemTypeId);
    }
}