<?php

namespace Kunstmaan\MediaBundle\Tests\Helper;

use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\MediaManager;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-09-28 at 14:19:22.
 *
 * @coversNothing
 */
class MediaManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MediaManager
     */
    protected $object;

    private $defaultHandler;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @covers \Kunstmaan\MediaBundle\Helper\MediaManager::setDefaultHandler
     */
    protected function setUp()
    {
        $this->defaultHandler = $this->getMockForAbstractClass('Kunstmaan\MediaBundle\Helper\Media\AbstractMediaHandler', [0]);
        $this->defaultHandler
            ->expects($this->any())
            ->method('canHandle')
            ->will($this->returnValue(true));
        $this->defaultHandler
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('DefaultHandler'));
        $this->defaultHandler
            ->expects($this->any())
            ->method('getType')
            ->will($this->returnValue('any/type'));
        $this->object = new MediaManager();
        $this->object->setDefaultHandler($this->defaultHandler);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers \Kunstmaan\MediaBundle\Helper\MediaManager::addHandler
     * @covers \Kunstmaan\MediaBundle\Helper\MediaManager::getHandler
     */
    public function testAddHandler()
    {
        $media = new Media();
        $handler = $this->getCustomHandler($media);
        $this->object->addHandler($handler);
        $this->assertSame($handler, $this->object->getHandler($media));
    }

    /**
     * @covers \Kunstmaan\MediaBundle\Helper\MediaManager::getHandlerForType
     */
    public function testGetHandlerForType()
    {
        $handler = $this->getCustomHandler();
        $this->object->addHandler($handler);
        $this->assertSame($handler, $this->object->getHandlerForType('custom/type'));
        $this->assertSame($this->defaultHandler, $this->object->getHandlerForType('unknown/type'));
    }

    /**
     * @covers \Kunstmaan\MediaBundle\Helper\MediaManager::getHandlers
     */
    public function testGetHandlers()
    {
        $handler = $this->getCustomHandler();
        $this->object->addHandler($handler);
        $handlers = $this->object->getHandlers();
        $this->assertCount(1, $handlers);
        $this->assertSame($handler, current($handlers));
    }

    /**
     * @covers \Kunstmaan\MediaBundle\Helper\MediaManager::prepareMedia
     */
    public function testPrepareMediaWithDefaultHandler()
    {
        $media = new Media();
        $this->defaultHandler
            ->expects($this->any())
            ->method('prepareMedia')
            ->with($this->equalTo($media));
        $this->object->prepareMedia($media);
    }

    /**
     * @covers \Kunstmaan\MediaBundle\Helper\MediaManager::prepareMedia
     */
    public function testPrepareMediaWithCustomHandler()
    {
        $media = new Media();
        $handler = $this->getCustomHandler($media);
        $handler
            ->expects($this->once())
            ->method('prepareMedia')
            ->with($this->equalTo($media));
        $this->object->addHandler($handler);
        $this->object->prepareMedia($media);
    }

    /**
     * @covers \Kunstmaan\MediaBundle\Helper\MediaManager::saveMedia
     */
    public function testSaveMediaWithDefaultHandler()
    {
        $media = new Media();
        $this->defaultHandler
            ->expects($this->once())
            ->method('saveMedia')
            ->with($this->equalTo($media));
        $this->object->saveMedia($media, true);

        $this->defaultHandler
            ->expects($this->once())
            ->method('updateMedia')
            ->with($this->equalTo($media));
        $this->object->saveMedia($media);
    }

    /**
     * @covers \Kunstmaan\MediaBundle\Helper\MediaManager::saveMedia
     */
    public function testCreateMediaWithCustomHandler()
    {
        $media = new Media();
        $handler = $this->getCustomHandler($media);
        $handler
            ->expects($this->once())
            ->method('saveMedia')
            ->with($this->equalTo($media));
        $this->object->addHandler($handler);
        $this->object->saveMedia($media, true);
    }

    /**
     * @covers \Kunstmaan\MediaBundle\Helper\MediaManager::saveMedia
     */
    public function testUpdateMediaWithCustomHandler()
    {
        $media = new Media();
        $handler = $this->getCustomHandler($media);
        $handler
            ->expects($this->once())
            ->method('updateMedia')
            ->with($this->equalTo($media));
        $this->object->addHandler($handler);
        $this->object->saveMedia($media);
    }

    /**
     * @covers \Kunstmaan\MediaBundle\Helper\MediaManager::removeMedia
     */
    public function testRemoveMediaWithDefaultHandler()
    {
        $media = new Media();
        $this->defaultHandler
            ->expects($this->once())
            ->method('removeMedia')
            ->with($this->equalTo($media));
        $this->object->removeMedia($media);
    }

    /**
     * @covers \Kunstmaan\MediaBundle\Helper\MediaManager::removeMedia
     */
    public function testRemoveMediaWithCustomHandler()
    {
        $media = new Media();
        $handler = $this->getCustomHandler($media);
        $handler
            ->expects($this->once())
            ->method('removeMedia')
            ->with($this->equalTo($media));
        $this->object->addHandler($handler);
        $this->object->removeMedia($media);
    }

    /**
     * @covers \Kunstmaan\MediaBundle\Helper\MediaManager::getHandler
     */
    public function testGetHandlerWithDefaultHandler()
    {
        $media = new Media();
        $this->assertSame($this->defaultHandler, $this->object->getHandler($media));
    }

    /**
     * @covers \Kunstmaan\MediaBundle\Helper\MediaManager::getHandler
     */
    public function testGetHandlerWithCustomHandler()
    {
        $media = new Media();
        $handler = $this->getCustomHandler($media);
        $this->object->addHandler($handler);
        $this->assertSame($handler, $this->object->getHandler($media));
    }

    /**
     * @covers \Kunstmaan\MediaBundle\Helper\MediaManager::createNew
     */
    public function testCreateNew()
    {
        $media = new Media();
        $data = new \StdClass();
        $this->assertNull($this->object->createNew($data));

        $handler1 = $this->getCustomHandler(null, 'CustomHandler1');
        $handler1
            ->expects($this->once())
            ->method('createNew')
            ->with($this->equalTo($data))
            ->will($this->returnValue(false));
        $this->object->addHandler($handler1);

        $handler2 = $this->getCustomHandler(null, 'CustomHandler2');
        $handler2
            ->expects($this->once())
            ->method('createNew')
            ->with($this->equalTo($data))
            ->will($this->returnValue($media));
        $this->object->addHandler($handler2);

        $this->assertSame($media, $this->object->createNew($data));
    }

    /**
     * @covers \Kunstmaan\MediaBundle\Helper\MediaManager::getFolderAddActions
     */
    public function testGetFolderAddActions()
    {
        $actions = [];
        $this->assertSame($actions, $this->object->getFolderAddActions());

        $actions = ['action1', 'action2'];
        $handler = $this->getCustomHandler();
        $handler
            ->expects($this->once())
            ->method('getAddFolderActions')
            ->will($this->returnValue($actions));
        $this->object->addHandler($handler);
        $this->assertSame($actions, $this->object->getFolderAddActions());
    }

    /**
     * @param object $media
     * @param string $name
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getCustomHandler($media = null, $name = null)
    {
        $handler = $this->getMockForAbstractClass('Kunstmaan\MediaBundle\Helper\Media\AbstractMediaHandler', [1]);
        if (empty($name)) {
            $name = 'CustomHandler';
        }
        $handler
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));
        $handler
            ->expects($this->any())
            ->method('getType')
            ->will($this->returnValue('custom/type'));
        if (null !== $media) {
            $handler
                ->expects($this->any())
                ->method('canHandle')
                ->with($this->equalTo($media))
                ->will($this->returnValue(true));
        }

        return $handler;
    }
}
