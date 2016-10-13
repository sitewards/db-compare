<?php

/**
 * @category    Sitewards
 * @package     Sitewards_DBCompare
 * @copyright   Copyright (c) Sitewards GmbH (http://www.sitewards.com/)
 */

namespace Sitewards\DBCompare\Exception;

class NoItemWorkerMappingException extends \RuntimeException
{
    /**
     * @param string $sItemTypeId
     * @param string $sExceptionMessage
     * @param int $iExceptionCode
     * @param \Exception|null $oPrevious
     */
    public function __construct($sItemTypeId, $sExceptionMessage = '', $iExceptionCode = 0, \Exception $oPrevious = null)
    {
        $sExceptionMessage = sprintf(
            'No Item Worker found for type: %s',
            $sItemTypeId
        );
        parent::__construct($sExceptionMessage, $iExceptionCode, $oPrevious);
    }
}