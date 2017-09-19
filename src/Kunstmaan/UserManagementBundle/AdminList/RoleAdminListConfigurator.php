<?php

namespace Kunstmaan\UserManagementBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\StringFilterType;

/**
 * Role admin list configurator used to manage {@link Role} in the admin.
 */
class RoleAdminListConfigurator extends AbstractSettingsAdminListConfigurator
{
    /**
     * Build filters for admin list.
     */
    public function buildFilters()
    {
        $this->addFilter('role', new StringFilterType('role'), 'kuma_user.role.adminlist.filter.role');
        $this->addFilter('name', new StringFilterType('name'), 'kuma_user.role.adminlist.filter.name');
    }

    /**
     * Configure the visible columns.
     */
    public function buildFields()
    {
        $this->addField('name', 'kuma_user.role.adminlist.header.name', true, 'KunstmaanUserManagementBundle:Roles:name.html.twig');
    }

    /**
     * Get repository name.
     *
     * @return string
     */
    public function getEntityName()
    {
        return 'Role';
    }

    public function getListTitle()
    {
        return 'kuma_user.roles.adminlist.title';
    }
}
