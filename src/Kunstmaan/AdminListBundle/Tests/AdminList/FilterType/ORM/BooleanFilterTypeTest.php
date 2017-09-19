<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\FilterType\ORM;

use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\BooleanFilterType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-09-26 at 13:21:34.
 *
 * @coversNothing
 */
class BooleanFilterTypeTest extends ORMFilterTypeTestCase
{
    /**
     * @var BooleanFilterType
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new BooleanFilterType('boolean', 'b');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers \Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\BooleanFilterType::bindRequest
     */
    public function testBindRequest()
    {
        $request = new Request(['filter_value_boolean' => 'true']);

        $data = [];
        $uniqueId = 'boolean';
        $this->object->bindRequest($request, $data, $uniqueId);

        $this->assertSame(['value' => 'true'], $data);
    }

    /**
     * @param mixed $value
     *
     * @covers \Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\BooleanFilterType::apply
     * @dataProvider applyDataProvider
     */
    public function testApply($value)
    {
        $qb = $this->getQueryBuilder();
        $qb->select('b')
            ->from('Entity', 'b');
        $this->object->setQueryBuilder($qb);
        $this->object->apply(['value' => $value], 'boolean');

        $this->assertSame("SELECT b FROM Entity b WHERE b.boolean = $value", $qb->getDQL());
    }

    /**
     * @return array
     */
    public static function applyDataProvider()
    {
        return [
            ['true'],
            ['false'],
        ];
    }

    /**
     * @covers \Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\BooleanFilterType::getTemplate
     */
    public function testGetTemplate()
    {
        $this->assertSame('KunstmaanAdminListBundle:FilterType:booleanFilter.html.twig', $this->object->getTemplate());
    }
}
