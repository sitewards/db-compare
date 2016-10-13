<?php

namespace Sitewards\DBCompare\Validator;

use Sitewards\DBCompare\Exception\FileNotFoundException;
use Sitewards\DBCompare\Exception\FileNotReadableException;

class FilePathTest extends \PHPUnit_Framework_TestCase
{
    public function testDoValidation()
    {
        $oValidator = new FilePath();
        $sAnswer = $oValidator->doValidation('tests/Sitewards/DBCompare/Validator/FilePathTest.php');
        $this->assertEquals('tests/Sitewards/DBCompare/Validator/FilePathTest.php', $sAnswer);
    }

    public function testDoValidationFileNotFoundException()
    {
        $this->expectException(FileNotFoundException::class);
        $oValidator = new FilePath();
        $oValidator->doValidation('something/that/is/not/there.txt');
    }
}