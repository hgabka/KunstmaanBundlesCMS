<?php

namespace Kunstmaan\NodeBundle\Tests;

use Kunstmaan\UtilitiesBundle\Helper\Slugifier;
use Kunstmaan\UtilitiesBundle\Helper\SlugifierInterface;

/**
 * SlugifierTest.
 *
 * @coversNothing
 */
class SlugifierTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SlugifierInterface
     */
    private $slugifier;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->slugifier = new Slugifier();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @param string $text    The text to slugify
     * @param string $default The default alternative
     * @param string $result  The slug it should generate
     *
     * @dataProvider getSlugifyData
     */
    public function testSlugify($text, $default, $result)
    {
        if (null !== $default) {
            $this->assertSame($result, $this->slugifier->slugify($text, $default));
        } else {
            $this->assertSame($result, $this->slugifier->slugify($text));
        }
    }

    /**
     * Provides data to the {@link testSlugify} function.
     *
     * @return array
     */
    public function getSlugifyData()
    {
        return [
            ['', '', ''],
            ['', null, ''],
            ['test', '', 'test'],
            ['een titel met spaties', '', 'een-titel-met-spaties'],
            ['à partir d\'aujourd\'hui', null, 'a-partir-d-aujourd-hui'],
            ['CaPs ShOulD be LoweRCasEd', null, 'caps-should-be-lowercased'],
            ['áàäåéèëíìïóòöúùüñßæ', null, 'aaaaeeeiiiooouuunssae'],
            ['polish-ążśźęćńół', null, 'polish-azszecnol'],
        ];
    }
}
