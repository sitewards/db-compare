<?php

namespace Sitewards\DBCompare\Test;

use Sitewards\DBCompare\CompareApplication;

class CompareApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that the arguments on the command are reset to 0
     */
    public function testCommandHasNoArguments()
    {
        $oCompareApp    = new CompareApplication();
        $oDefinition    = $oCompareApp->getDefinition();
        $iArgumentCount = $oDefinition->getArgumentCount();
        $this->assertEquals(0, $iArgumentCount);
    }
}