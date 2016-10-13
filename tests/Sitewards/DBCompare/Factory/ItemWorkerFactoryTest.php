<?php

namespace Sitewards\DBCompare\Factory;

use Doctrine\DBAL\Connection;
use Sitewards\DBCompare\Exception\NoItemWorkerMappingException;
use Sitewards\DBCompare\Worker\Item\StoreConfigWorker;

class ItemWorkerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateById()
    {
        $oConnection  = $this->createMock(Connection::class);
        $oItemFactory = new ItemWorkerFactory();
        $oWorker      = $oItemFactory->createById(
            'system config',
            $oConnection
        );
        $this->assertInstanceOf(
            StoreConfigWorker::class,
            $oWorker
        );
    }

    public function testCreateByIdNoItemWorkerMappingException()
    {
        $oConnection = $this->createMock(Connection::class);
        $this->expectException(NoItemWorkerMappingException::class);
        $oItemFactory = new ItemWorkerFactory();
        $oItemFactory->createById(
            'random text',
            $oConnection
        );
    }
}