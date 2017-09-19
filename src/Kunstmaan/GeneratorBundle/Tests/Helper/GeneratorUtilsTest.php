<?php

namespace Kunstmaan\GeneratorBundle\Tests\Helper;

use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-10-03 at 09:50:30.
 *
 * @coversNothing
 */
class GeneratorUtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GeneratorUtils
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new GeneratorUtils();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers \Kunstmaan\GeneratorBundle\Helper\GeneratorUtils::cleanPrefix
     */
    public function testCleanPrefixWhenPrefixEmpty()
    {
        $response = GeneratorUtils::cleanPrefix('');
        $this->assertNull($response);
    }

    /**
     * @covers \Kunstmaan\GeneratorBundle\Helper\GeneratorUtils::cleanPrefix
     */
    public function testCleanPrefixShouldConvertToLowercase()
    {
        $response = GeneratorUtils::cleanPrefix('TEST');
        $this->assertSame('test_', $response);
    }

    /**
     * @covers \Kunstmaan\GeneratorBundle\Helper\GeneratorUtils::cleanPrefix
     */
    public function testCleanPrefixShouldAppendUnderscore()
    {
        $response = GeneratorUtils::cleanPrefix('test');
        $this->assertSame('test_', $response);
    }

    /**
     * @covers \Kunstmaan\GeneratorBundle\Helper\GeneratorUtils::cleanPrefix
     */
    public function testCleanPrefixShouldAppendUnderscoreOnlyWhenNeeded()
    {
        $response = GeneratorUtils::cleanPrefix('test_');
        $this->assertSame('test_', $response);
    }

    /**
     * @covers \Kunstmaan\GeneratorBundle\Helper\GeneratorUtils::cleanPrefix
     */
    public function testCleanPrefixShouldLeaveUnderscoresInPlace()
    {
        $response = GeneratorUtils::cleanPrefix('test_bundle');
        $this->assertSame('test_bundle_', $response);
    }

    /**
     * @covers \Kunstmaan\GeneratorBundle\Helper\GeneratorUtils::cleanPrefix
     */
    public function testCleanPrefixShouldLeaveSingleUnderscore()
    {
        $response = GeneratorUtils::cleanPrefix('test____');
        $this->assertSame('test_', $response);
    }

    /**
     * @covers \Kunstmaan\GeneratorBundle\Helper\GeneratorUtils::cleanPrefix
     */
    public function testShouldConvertOnlyUnderscoresToNull()
    {
        $response = GeneratorUtils::cleanPrefix('____');
        $this->assertSame(null, $response);
    }

    /**
     * @covers \Kunstmaan\GeneratorBundle\Helper\GeneratorUtils::cleanPrefix
     */
    public function testSpacesShouldCreateEmptyPrefix()
    {
        $response = GeneratorUtils::cleanPrefix('  ');
        $this->assertSame(null, $response);
    }
}
