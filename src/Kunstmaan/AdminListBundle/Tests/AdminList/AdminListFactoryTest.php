<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList;

use Kunstmaan\AdminListBundle\AdminList\AdminListFactory;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\AdminList\Configurator\ExportListConfiguratorInterface;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-09-26 at 13:21:32.
 *
 * @coversNothing
 */
class AdminListFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AdminListFactory
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new AdminListFactory();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers \Kunstmaan\AdminListBundle\AdminList\AdminListFactory::createList
     */
    public function testCreateList()
    {
        // @var AdminListConfiguratorInterface $mockConfig
        $mockConfig = $this->getMock('Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface');
        $list = $this->object->createList($mockConfig);

        $this->assertInstanceOf('Kunstmaan\AdminListBundle\AdminList\AdminList', $list);
    }

    /**
     * @covers \Kunstmaan\AdminListBundle\AdminList\AdminListFactory::createExportList
     */
    public function testCreateExportList()
    {
        // @var ExportListConfiguratorInterface $mockConfig
        $mockConfig = $this->getMock('Kunstmaan\AdminListBundle\AdminList\Configurator\ExportListConfiguratorInterface');
        $list = $this->object->createExportList($mockConfig);

        $this->assertInstanceOf('Kunstmaan\AdminListBundle\AdminList\ExportList', $list);
    }
}
