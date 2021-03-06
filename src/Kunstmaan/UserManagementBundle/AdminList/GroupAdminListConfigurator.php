<?php

namespace Kunstmaan\UserManagementBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\StringFilterType;

/**
 * Group admin list configurator used to manage {@link Group} in the admin.
 */
class GroupAdminListConfigurator extends AbstractSettingsAdminListConfigurator
{
    /**
     * Build filters for admin list.
     */
    public function buildFilters()
    {
        $this->addFilter('name', new StringFilterType('name'), 'kuma_user.group.adminlist.filter.name');
    }

    /**
     * Configure the visible columns.
     */
    public function buildFields()
    {
        $this->addField('name', 'kuma_user.group.adminlist.header.name', true);
        $this->addField('roleNames', 'kuma_user.group.adminlist.header.roles', false, 'KunstmaanUserManagementBundle:Groups:roles.html.twig');
    }

    /**
     * Get repository name.
     *
     * @return string
     */
    public function getEntityName()
    {
        return 'Group';
    }

    public function getListTitle()
    {
        return 'kuma_user.group.adminlist.title';
    }
}
