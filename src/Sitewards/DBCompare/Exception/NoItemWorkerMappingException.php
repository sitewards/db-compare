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
     * @param int $iItemTypeId
     * @param string $sExceptionMessage
     * @param int $iExceptionCode
     * @param \Exception|null $oPrevious
     */
    public function __construct($iItemTypeId, $sExceptionMessage = '', $iExceptionCode = 0, \Exception $oPrevious = null)
    {
        $sExceptionMessage = sprintf(
            'No Item Worker found for type: %d',
            $iItemTypeId
        );
        parent::__construct($sExceptionMessage, $iExceptionCode, $oPrevious);
    }
}