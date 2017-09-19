<?php

use Kunstmaan\TranslatorBundle\DependencyInjection\KunstmaanTranslatorExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @coversNothing
 */
class KunstmaanTranslatorExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var KunstmaanTranslatorExtension
     */
    private $extension;

    public function setUp()
    {
        parent::setUp();

        $this->extension = $this->getExtension();
    }

    public function testEnabledByDefault()
    {
        $container = $this->getContainer();
        $this->extension->load(['kuma_translator' => ['managed_locales' => ['nl']]], $container);
        $this->assertTrue($container->getParameter('kuma_translator.enabled'));
    }

    public function testDisabled()
    {
        $container = $this->getContainer();
        $this->extension->load(['kuma_translator' => ['enabled' => false]], $container);
        $this->assertFalse($container->hasParameter('kuma_translator.enabled'));
    }

    /**
     * Returns the Configuration to test.
     *
     * @return Configuration
     */
    protected function getExtension()
    {
        return new KunstmaanTranslatorExtension();
    }

    /**
     * @return ContainerBuilder
     */
    private function getContainer()
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.root_dir', '');
        $container->setParameter('kernel.bundles', []);
        $container->setParameter('kernel.debug', true);
        $container->setParameter('defaultlocale', 'en');

        return $container;
    }
}
