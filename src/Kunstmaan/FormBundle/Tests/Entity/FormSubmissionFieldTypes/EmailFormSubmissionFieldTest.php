<?php

namespace Kunstmaan\FormBundle\Tests\Entity\FormSubmissionFieldTypes;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\EmailFormSubmissionField;
use Kunstmaan\FormBundle\Form\EmailFormSubmissionType;

/**
 * Tests for EmailFormSubmissionField.
 *
 * @coversNothing
 */
class EmailFormSubmissionFieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EmailFormSubmissionField
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new EmailFormSubmissionField();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers \Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\EmailFormSubmissionField::getValue
     * @covers \Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\EmailFormSubmissionField::setValue
     */
    public function testSetGetValue()
    {
        $object = $this->object;
        $value = 'test@test.be';
        $object->setValue($value);
        $this->assertSame($value, $object->getValue());
    }

    /**
     * @covers \Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\EmailFormSubmissionField::getDefaultAdminType
     */
    public function testGetDefaultAdminType()
    {
        $this->assertSame(EmailFormSubmissionType::class, $this->object->getDefaultAdminType());
    }

    /**
     * @covers \Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\EmailFormSubmissionField::__toString
     */
    public function testToString()
    {
        $stringValue = $this->object->__toString();
        $this->assertNotNull($stringValue);
        $this->assertInternalType('string', $stringValue);
    }
}
