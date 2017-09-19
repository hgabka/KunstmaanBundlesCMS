<?php

use Kunstmaan\SearchBundle\DependencyInjection\KunstmaanSearchExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @coversNothing
 */
class KunstmaanSearchExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var KunstmaanSearchExtension
     */
    private $extension;

    public function setUp()
    {
        parent::setUp();

        $this->extension = $this->getExtension();
    }

    public function testGetConfig()
    {
        $container = $this->getContainer();
        $this->extension->load([[]], $container);
        $this->assertTrue($container->hasParameter('analyzer_languages'));
        $this->assertInternalType('array', $container->getParameter('analyzer_languages'));

        $analyzers = $container->getParameter('analyzer_languages');
        $this->assertArrayHasKey('ar', $analyzers);
        $this->assertSame('arabic', $analyzers['ar']['analyzer']);
    }

    /**
     * Returns the Configuration to test.
     *
     * @return Configuration
     */
    protected function getExtension()
    {
        return new KunstmaanSearchExtension();
    }

    /**
     * @return ContainerBuilder
     */
    private function getContainer()
    {
        return new ContainerBuilder();
    }
}
