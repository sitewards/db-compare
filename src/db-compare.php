<?php

/**
 * @category    Sitewards
 * @package     Sitewards_DBCompare
 * @copyright   Copyright (c) Sitewards GmbH (http://www.sitewards.com/)
 */

require __DIR__.'/../vendor/autoload.php';

use Sitewards\DBCompare\CompareApplication;

$oDBCompare = new CompareApplication();

// ... register commands

$oDBCompare->run();