<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\Security\Acl\Permission;

use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder;

/**
 * MaskBuilderTest.
 *
 * @coversNothing
 */
class MaskBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param mixed $invalidMask
     *
     * @covers \Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder::__construct
     * @dataProvider getInvalidConstructorData
     */
    public function testSlugify($invalidMask)
    {
        new MaskBuilder($invalidMask);
    }

    /**
     * Provides data to the {@link testSlugify} function.
     *
     * @return array
     */
    public function getInvalidConstructorData()
    {
        return [
            [234.463],
            ['asdgasdf'],
            [[]],
            [new \stdClass()],
        ];
    }

    /**
     * @covers \Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder::__construct
     */
    public function testConstructorWithoutArguments()
    {
        $builder = new MaskBuilder();

        $this->assertSame(0, $builder->get());
    }

    /**
     * @covers \Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder::__construct
     */
    public function testConstructor()
    {
        $builder = new MaskBuilder(123456);

        $this->assertSame(123456, $builder->get());
    }

    /**
     * @covers \Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder::add
     * @covers \Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder::remove
     */
    public function testAddAndRemove()
    {
        $builder = new MaskBuilder();

        $builder
            ->add('view')
            ->add('eDiT')
            ->add('puBLisH');
        $mask = $builder->get();

        $this->assertSame(MaskBuilder::MASK_VIEW, $mask & MaskBuilder::MASK_VIEW);
        $this->assertSame(MaskBuilder::MASK_EDIT, $mask & MaskBuilder::MASK_EDIT);
        $this->assertSame(MaskBuilder::MASK_PUBLISH, $mask & MaskBuilder::MASK_PUBLISH);
        $this->assertSame(0, $mask & MaskBuilder::MASK_DELETE);
        $this->assertSame(0, $mask & MaskBuilder::MASK_UNPUBLISH);

        $builder->remove('edit')->remove('PUblish');
        $mask = $builder->get();
        $this->assertSame(0, $mask & MaskBuilder::MASK_EDIT);
        $this->assertSame(0, $mask & MaskBuilder::MASK_PUBLISH);
        $this->assertSame(MaskBuilder::MASK_VIEW, $mask & MaskBuilder::MASK_VIEW);
    }

    /**
     * @covers \Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder::add
     * @covers \Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder::getPattern
     */
    public function testGetPattern()
    {
        $builder = new MaskBuilder();
        $this->assertSame(MaskBuilder::ALL_OFF, $builder->getPattern());

        $builder->add('view');
        $this->assertSame(str_repeat('.', 31).'V', $builder->getPattern());

        $builder->add('publish');
        $this->assertSame(str_repeat('.', 27).'P...V', $builder->getPattern());

        $builder->add(1 << 10);
        $this->assertSame(str_repeat('.', 21).MaskBuilder::ON.'.....P...V', $builder->getPattern());
    }

    /**
     * @covers \Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder::get
     * * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder::reset
     */
    public function testReset()
    {
        $builder = new MaskBuilder();
        $this->assertSame(0, $builder->get());

        $builder->add('view');
        $this->assertTrue($builder->get() > 0);

        $builder->reset();
        $this->assertSame(0, $builder->get());
    }

    /**
     * @covers \Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder::add
     */
    public function testAddWithInvalidMask()
    {
        $builder = new MaskBuilder();
        $builder->add(null);
    }

    /**
     * @covers \Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder::remove
     */
    public function testRemoveWithInvalidMask()
    {
        $builder = new MaskBuilder();
        $builder->remove(null);
    }

    /**
     * @covers \Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder::getCode
     */
    public function testGetCode()
    {
        $code = MaskBuilder::getCode(MaskBuilder::MASK_DELETE);
        $this->assertSame(MaskBuilder::CODE_DELETE, $code);

        $code = MaskBuilder::getCode(MaskBuilder::MASK_UNPUBLISH);
        $this->assertSame(MaskBuilder::CODE_UNPUBLISH, $code);
    }

    /**
     * @covers \Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder::getCode
     */
    public function testGetCodeWithInvalidMask()
    {
        MaskBuilder::getCode(null);
    }

    /**
     * @covers \Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder::has
     */
    public function testHas()
    {
        $builder = new MaskBuilder();
        $builder->add('edit')
            ->add('view');

        $this->assertSame(true, $builder->has(MaskBuilder::MASK_EDIT));
        $this->assertSame(true, $builder->has('view'));
        $this->assertSame(false, $builder->has(MaskBuilder::MASK_UNPUBLISH));
        $this->assertSame(false, $builder->has('publish'));
    }

    /**
     * @covers \Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder::has
     * @covers \Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder::add
     */
    public function testHasWithInvalidMask()
    {
        $builder = new MaskBuilder();
        $builder->add('edit')
            ->add('view');

        $builder->has(null);
    }
}
