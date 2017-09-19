<?php

namespace Kunstmaan\RedirectBundle\Tests\Entity;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Field;
use Kunstmaan\RedirectBundle\AdminList\RedirectAdminListConfigurator;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-04-16 at 18:05:47.
 *
 * @coversNothing
 */
class RedirectAdminListConfiguratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var AclHelper
     */
    protected $aclHelper;

    /**
     * @var RedirectAdminListConfigurator
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $domainConfiguration = $this->getMockBuilder('Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface')
            ->disableOriginalConstructor()->getMock();

        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()->getMock();
        $this->aclHelper = $this->getMockBuilder('Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper')
            ->disableOriginalConstructor()->getMock();

        $this->object = new RedirectAdminListConfigurator($this->em, $this->aclHelper, $domainConfiguration);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers \Kunstmaan\RedirectBundle\AdminList\RedirectAdminListConfigurator::buildFields
     */
    public function testBuildFields()
    {
        $this->object->buildFields();
        $fields = $this->object->getFields();
        $this->assertSame(3, count($fields));
        $fieldNames = array_map(
            function (Field $field) {
                return $field->getName();
            },
            $fields
        );
        $this->assertSame(['origin', 'target', 'permanent'], $fieldNames);
    }

    /**
     * @covers \Kunstmaan\RedirectBundle\AdminList\RedirectAdminListConfigurator::buildFilters
     */
    public function testBuildFilters()
    {
        $filterBuilder = $this->getMock('Kunstmaan\AdminListBundle\AdminList\FilterBuilder');
        $filterBuilder
            ->expects($this->at(0))
            ->method('add')
            ->with('origin');
        $filterBuilder
            ->expects($this->at(1))
            ->method('add')
            ->with('target');
        $filterBuilder
            ->expects($this->at(2))
            ->method('add')
            ->with('permanent');
        $this->object->setFilterBuilder($filterBuilder);
        $this->object->buildFilters();
    }

    /**
     * @covers \Kunstmaan\RedirectBundle\AdminList\RedirectAdminListConfigurator::getBundleName
     */
    public function testGetBundleName()
    {
        $this->assertSame('KunstmaanRedirectBundle', $this->object->getBundleName());
    }

    /**
     * @covers \Kunstmaan\RedirectBundle\AdminList\RedirectAdminListConfigurator::getEntityName
     */
    public function testGetEntityName()
    {
        $this->assertSame('Redirect', $this->object->getEntityName());
    }
}
