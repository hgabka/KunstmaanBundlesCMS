<?php

namespace Kunstmaan\FormBundle\Tests\Entity\PageParts;

use ArrayObject;
use Kunstmaan\FormBundle\Entity\PageParts\FileUploadPagePart;
use Kunstmaan\FormBundle\Form\FileUploadPagePartAdminType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Tests for FileUploadPagePart.
 *
 * @coversNothing
 */
class FileUploadPagePartTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FileUploadPagePart
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new FileUploadPagePart();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers \Kunstmaan\FormBundle\Entity\PageParts\FileUploadPagePart::adaptForm
     */
    public function testAdaptForm()
    {
        $object = $this->object;
        $object->setRequired(true);

        $formBuilder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $formBuilder->expects($this->any())
            ->method('getData')
            ->will($this->returnValue([]));

        $fields = new ArrayObject();

        $this->assertTrue(0 === count($fields));
        // @var $formBuilder FormBuilderInterface
        $object->adaptForm($formBuilder, $fields, 0);
        $this->assertTrue(count($fields) > 0);
    }

    /**
     * @covers \Kunstmaan\FormBundle\Entity\PageParts\FileUploadPagePart::getDefaultView
     */
    public function testGetDefaultView()
    {
        $stringValue = $this->object->getDefaultView();
        $this->assertNotNull($stringValue);
        $this->assertInternalType('string', $stringValue);
    }

    /**
     * @covers \Kunstmaan\FormBundle\Entity\PageParts\FileUploadPagePart::getDefaultAdminType
     */
    public function testGetDefaultAdminType()
    {
        $this->assertSame(FileUploadPagePartAdminType::class, $this->object->getDefaultAdminType());
    }
}
