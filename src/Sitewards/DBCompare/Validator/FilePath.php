<?php

namespace Sitewards\DBCompare\Validator;

use Sitewards\DBCompare\Exception\FileNotFoundException;
use Sitewards\DBCompare\Exception\FileNotReadableException;

class FilePath
{
    /**
     * @param string $sAnswer
     * @return string
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     */
    public function doValidation($sAnswer)
    {
        if (!file_exists($sAnswer)) {
            throw new FileNotFoundException(
                sprintf(
                    'The file %s cannot be found',
                    $sAnswer
                )
            );
        }
        if (!is_readable($sAnswer)) {
            throw new FileNotReadableException(
                sprintf(
                    'The file %s cannot be read',
                    $sAnswer
                )
            );
        }
        return $sAnswer;
    }
}