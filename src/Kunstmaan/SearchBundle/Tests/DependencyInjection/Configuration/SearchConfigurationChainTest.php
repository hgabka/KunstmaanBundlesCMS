<?php

namespace Kunstmaan\SearchBundle\Tests\Configuration;

use Kunstmaan\SearchBundle\Configuration\SearchConfigurationChain;

/**
 * @coversNothing
 */
class SearchConfigurationChainTest extends \PHPUnit_Framework_TestCase
{
    public function testAddAndGetConfiguration()
    {
        $configuration = $this->getMock('Kunstmaan\SearchBundle\Configuration\SearchConfigurationInterface');

        $chain = new SearchConfigurationChain();
        $chain->addConfiguration($configuration, 'configuration');

        $this->assertSame($configuration, $chain->getConfiguration('configuration'));
    }
}
