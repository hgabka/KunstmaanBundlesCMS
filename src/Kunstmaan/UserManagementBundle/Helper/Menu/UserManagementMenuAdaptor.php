<?php

namespace Kunstmaan\UserManagementBundle\Helper\Menu;

use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserManagementMenuAdaptor implements MenuAdaptorInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * In this method you can add children for a specific parent, but also remove and change the already created children.
     *
     * @param MenuBuilder $menu      The MenuBuilder
     * @param MenuItem[]  &$children The current children
     * @param MenuItem    $parent    The parent Menu item
     * @param Request     $request   The Request
     */
    public function adaptChildren(MenuBuilder $menu, array &$children, MenuItem $parent = null, Request $request = null)
    {
        if (null === $parent) {
            return;
        } elseif ('KunstmaanAdminBundle_settings' === $parent->getRoute()) {
            if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
                $menuItem = new MenuItem($menu);
                $menuItem
                    ->setRoute('KunstmaanUserManagementBundle_settings_users')
                    ->setUniqueId('Users')
                    ->setLabel('settings.users')
                    ->setParent($parent);
                if (0 === stripos($request->attributes->get('_route'), $menuItem->getRoute())) {
                    $menuItem->setActive(true);
                    $parent->setActive(true);
                }
                $children[] = $menuItem;

                $menuItem = new MenuItem($menu);
                $menuItem
                    ->setRoute('KunstmaanUserManagementBundle_settings_groups')
                    ->setUniqueId('Groups')
                    ->setLabel('settings.groups')
                    ->setParent($parent);

                if (0 === stripos($request->attributes->get('_route'), $menuItem->getRoute())) {
                    $menuItem->setActive(true);
                    $parent->setActive(true);
                }
                $children[] = $menuItem;

                $menuItem = new MenuItem($menu);
                $menuItem
                    ->setRoute('KunstmaanUserManagementBundle_settings_roles')
                    ->setUniqueId('Roles')
                    ->setLabel('settings.roles')
                    ->setParent($parent);
                if (0 === stripos($request->attributes->get('_route'), $menuItem->getRoute())) {
                    $menuItem->setActive(true);
                    $parent->setActive(true);
                }
                $children[] = $menuItem;
            }
        } else {
            if ('KunstmaanUserManagementBundle_settings_users' === $parent->getRoute()) {
                if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
                    $menuItem = new MenuItem($menu);
                    $menuItem
                        ->setRoute('KunstmaanUserManagementBundle_settings_users_add')
                        ->setUniqueId('Add user')
                        ->setLabel('settings.user.add')
                        ->setParent($parent)
                        ->setAppearInNavigation(false);
                    if (0 === stripos($request->attributes->get('_route'), $menuItem->getRoute())) {
                        $menuItem->setActive(true);
                    }
                    $children[] = $menuItem;

                    $menuItem = new MenuItem($menu);
                    $menuItem
                        ->setRoute('KunstmaanUserManagementBundle_settings_users_edit')
                        ->setUniqueId('Edit user')
                        ->setLabel('settings.user.edit')
                        ->setParent($parent)
                        ->setAppearInNavigation(false);
                    if (0 === stripos($request->attributes->get('_route'), $menuItem->getRoute())) {
                        $menuItem->setActive(true);
                    }
                    $children[] = $menuItem;
                }
            } else {
                if ('KunstmaanUserManagementBundle_settings_groups' === $parent->getRoute()) {
                    if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
                        $menuItem = new MenuItem($menu);
                        $menuItem
                            ->setRoute('KunstmaanUserManagementBundle_settings_groups_add')
                            ->setUniqueId('Add group')
                            ->setLabel('settings.group.add')
                            ->setParent($parent)
                            ->setAppearInNavigation(false);
                        if (0 === stripos($request->attributes->get('_route'), $menuItem->getRoute())) {
                            $menuItem->setActive(true);
                        }
                        $children[] = $menuItem;

                        $menuItem = new MenuItem($menu);
                        $menuItem
                            ->setRoute('KunstmaanUserManagementBundle_settings_groups_edit')
                            ->setUniqueId('Edit group')
                            ->setLabel('settings.group.edit')
                            ->setParent($parent)
                            ->setAppearInNavigation(false);
                        if (0 === stripos($request->attributes->get('_route'), $menuItem->getRoute())) {
                            $menuItem->setActive(true);
                        }
                        $children[] = $menuItem;
                    }
                } else {
                    if ('KunstmaanUserManagementBundle_settings_roles' === $parent->getRoute()) {
                        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
                            $menuItem = new MenuItem($menu);
                            $menuItem
                                ->setRoute('KunstmaanUserManagementBundle_settings_roles_add')
                                ->setUniqueId('Add role')
                                ->setLabel('settings.role.add')
                                ->setParent($parent)
                                ->setAppearInNavigation(false);
                            if (0 === stripos($request->attributes->get('_route'), $menuItem->getRoute())) {
                                $menuItem->setActive(true);
                            }
                            $children[] = $menuItem;

                            $menuItem = new MenuItem($menu);
                            $menuItem
                                ->setRoute('KunstmaanUserManagementBundle_settings_roles_edit')
                                ->setUniqueId('Edit role')
                                ->setLabel('settings.role.edit')
                                ->setParent($parent)
                                ->setAppearInNavigation(false);
                            if (0 === stripos($request->attributes->get('_route'), $menuItem->getRoute())) {
                                $menuItem->setActive(true);
                            }
                            $children[] = $menuItem;
                        }
                    }
                }
            }
        }
    }
}
