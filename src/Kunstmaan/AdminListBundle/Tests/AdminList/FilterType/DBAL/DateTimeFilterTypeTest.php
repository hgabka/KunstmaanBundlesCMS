<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\FilterType\DBAL;

use Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\DateTimeFilterType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @coversNothing
 */
class DateTimeFilterTypeTest extends DBALFilterTypeTestCase
{
    /**
     * @var DateTimeFilterType
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new DateTimeFilterType('datetime', 'e');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @return array
     */
    public static function applyDataProvider()
    {
        return [
            ['before', '<= :var_datetime', ['date' => '14/04/2014', 'time' => '09:00'], '2014-04-14 09:00'],
            ['after', '> :var_datetime', ['date' => '14/04/2014', 'time' => '10:00'], '2014-04-14 10:00'],
        ];
    }

    /**
     * @covers \Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\DateTimeFilterType::bindRequest
     */
    public function testBindRequest()
    {
        $request = new Request([
            'filter_comparator_datetime' => 'before',
            'filter_value_datetime' => ['date' => '14/04/2014', 'time' => '09:00'],
        ]);

        $data = [];
        $uniqueId = 'datetime';
        $this->object->bindRequest($request, $data, $uniqueId);

        $this->assertSame(
            ['comparator' => 'before', 'value' => ['date' => '14/04/2014', 'time' => '09:00']],
            $data
        );
    }

    /**
     * @param string $comparator  The comparator
     * @param string $whereClause The where clause
     * @param mixed  $value       The value
     * @param mixed  $testValue   The test value
     *
     * @covers       \Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\DateTimeFilterType::apply
     * @dataProvider applyDataProvider
     */
    public function testApply($comparator, $whereClause, $value, $testValue)
    {
        $qb = $this->getQueryBuilder();
        $qb->select('*')
            ->from('entity', 'e');
        $this->object->setQueryBuilder($qb);
        $this->object->apply(['comparator' => $comparator, 'value' => $value], 'datetime');

        $this->assertSame("SELECT * FROM entity e WHERE e.datetime $whereClause", $qb->getSQL());
        $this->assertSame($testValue, $qb->getParameter('var_datetime'));
    }

    /**
     * @covers \Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\DateTimeFilterType::getTemplate
     */
    public function testGetTemplate()
    {
        $this->assertSame(
            'KunstmaanAdminListBundle:FilterType:dateTimeFilter.html.twig',
            $this->object->getTemplate()
        );
    }
}
