<?php

namespace Kunstmaan\GeneratorBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Kunstmaan\AdminBundle\Entity\Role;

/**
 * Fixture for creation the basic roles.
 */
class RoleFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    const REFERENCE_PERMISSIONMANAGER_ROLE = 'permissionmanager-role';
    const REFERENCE_ADMIN_ROLE = 'admin-role';
    const REFERENCE_SUPERADMIN_ROLE = 'superadmin-role';
    const REFERENCE_GUEST_ROLE = 'guest-role';

    /**
     * Load data fixtures with the passed EntityManager.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $role1 = $this->createRole($manager, 'ROLE_PERMISSIONMANAGER', 'settings.role.permission_manager');
        $role2 = $this->createRole($manager, 'ROLE_ADMIN', 'settings.role.admin');
        $role3 = $this->createRole($manager, 'ROLE_SUPER_ADMIN', 'settings.role.super_admin');
        $role4 = $this->createRole($manager, 'IS_AUTHENTICATED_ANONYMOUSLY', 'settings.role.anonymous');

        $manager->flush();

        $this->addReference(self::REFERENCE_PERMISSIONMANAGER_ROLE, $role1);
        $this->addReference(self::REFERENCE_ADMIN_ROLE, $role2);
        $this->addReference(self::REFERENCE_SUPERADMIN_ROLE, $role3);
        $this->addReference(self::REFERENCE_GUEST_ROLE, $role4);
    }

    /**
     * Get the order of this fixture.
     *
     * @return int
     */
    public function getOrder()
    {
        return 1;
    }

    /**
     * Create a role.
     *
     * @param ObjectManager $manager The object manager
     * @param string        $role    The role
     * @param string        $name    The name of the role
     *
     * @return Role
     */
    private function createRole(ObjectManager $manager, string $role, string $name)
    {
        $role = new Role($role);
        $role->setName($name);

        $manager->persist($role);

        return $role;
    }
}
