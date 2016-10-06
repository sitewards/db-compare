<?php

namespace Sitewards\DBCompare\Factory;

use Sitewards\DBCompare\Worker\Item\StoreConfigWorker;
use Sitewards\DBCompare\Worker\Item\EmailTemplateWorker;
use Sitewards\DBCompare\Exception\NoItemWorkerMappingException;
use Doctrine\DBAL\Connection;

class ItemFactory
{
    /**
     * @param string $sItemTypeId
     * @param Connection $oConnection
     * @return EmailTemplateWorker|StoreConfigWorker
     */
    public static function createById($sItemTypeId, Connection $oConnection)
    {
        if ($sItemTypeId === StoreConfigWorker::S_WORKER_ID) {
            return new StoreConfigWorker($oConnection);
        } elseif ($sItemTypeId === EmailTemplateWorker::S_WORKER_ID) {
            return new EmailTemplateWorker($oConnection);
        }
        throw new NoItemWorkerMappingException($sItemTypeId);
    }
}