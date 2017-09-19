<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\FilterType\DBAL;

use Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\NumberFilterType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-09-26 at 13:21:33.
 *
 * @coversNothing
 */
class NumberFilterTypeTest extends DBALFilterTypeTestCase
{
    /**
     * @var NumberFilterType
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new NumberFilterType('number', 'e');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers \Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\NumberFilterType::bindRequest
     */
    public function testBindRequest()
    {
        $request = new Request(['filter_comparator_number' => 'eq', 'filter_value_number' => 1]);

        $data = [];
        $uniqueId = 'number';
        $this->object->bindRequest($request, $data, $uniqueId);

        $this->assertSame(['comparator' => 'eq', 'value' => 1], $data);
    }

    /**
     * @param string $comparator  The comparator
     * @param string $whereClause The where clause
     * @param mixed  $value       The value
     * @param mixed  $testValue   The test value
     *
     * @covers \Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\NumberFilterType::apply
     * @dataProvider applyDataProvider
     */
    public function testApply($comparator, $whereClause, $value, $testValue)
    {
        $qb = $this->getQueryBuilder();
        $qb->select('*')
            ->from('entity', 'e');
        $this->object->setQueryBuilder($qb);
        $this->object->apply(['comparator' => $comparator, 'value' => $value], 'number');

        $this->assertSame("SELECT * FROM entity e WHERE e.number $whereClause", $qb->getSQL());
        if ($testValue) {
            $this->assertSame($value, $qb->getParameter('var_number'));
        }
    }

    /**
     * @return array
     */
    public static function applyDataProvider()
    {
        return [
            ['eq', '= :var_number', 1, true],
            ['neq', '<> :var_number', 2, true],
            ['lt', '< :var_number', 3, true],
            ['lte', '<= :var_number', 4, true],
            ['gt', '> :var_number', 5, true],
            ['gte', '>= :var_number', 6, true],
            ['isnull', 'IS NULL', 0, false],
            ['isnotnull', 'IS NOT NULL', 0, false],
        ];
    }

    /**
     * @covers \Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\NumberFilterType::getTemplate
     */
    public function testGetTemplate()
    {
        $this->assertSame('KunstmaanAdminListBundle:FilterType:numberFilter.html.twig', $this->object->getTemplate());
    }
}
