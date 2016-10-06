<?php

namespace Sitewards\DBCompare\Factory;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class DBConnectionFactory
{
    /**
     * @param string $sDBUser
     * @param string $sDBPassword
     * @return Connection
     */
    public static function getDatabaseConnection($sDBUser, $sDBPassword)
    {
        $oDBConfig = new Configuration();
        $aConnectionParams = [
            'user' => $sDBUser,
            'password' => $sDBPassword,
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
        ];
        return DriverManager::getConnection($aConnectionParams, $oDBConfig);
    }
}