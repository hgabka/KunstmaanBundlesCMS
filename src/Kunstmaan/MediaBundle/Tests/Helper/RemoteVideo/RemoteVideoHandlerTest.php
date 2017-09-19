<?php

namespace Kunstmaan\MediaBundle\Tests\Helper\RemoteVideo;

use Kunstmaan\MediaBundle\Helper\RemoteVideo\RemoteVideoHandler;

/**
 * @coversNothing
 */
class RemoteVideoHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $data
     * @param $expected
     * @param mixed $url
     * @param mixed $type
     * @param mixed $code
     *
     * @dataProvider provider
     */
    public function testYoutubeUrl($url, $type, $code)
    {
        $handler = new RemoteVideoHandler(0);

        $result = $handler->createNew($url);

        $this->assertInstanceOf('Kunstmaan\MediaBundle\Entity\Media', $result);
        $this->assertSame($type, $result->getMetadataValue('type'));
        $this->assertSame($code, $result->getMetadataValue('code'));
    }

    public function provider()
    {
        return [
            ['https://youtu.be/jPDHAXV8E6w', 'youtube', 'jPDHAXV8E6w'],
            ['https://www.youtube.com/watch?v=jPDHAXV8E6w', 'youtube', 'jPDHAXV8E6w'],
        ];
    }
}
