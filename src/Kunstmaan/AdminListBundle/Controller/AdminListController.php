<?php

namespace Kunstmaan\AdminListBundle\Controller;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Entity\EntityInterface;
use Kunstmaan\AdminBundle\Event\AdaptSimpleFormEvent;
use Kunstmaan\AdminBundle\Event\Events;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\ItemAction\SimpleItemAction;
use Kunstmaan\AdminListBundle\AdminList\SortableInterface;
use Kunstmaan\AdminListBundle\Event\AdminListEvent;
use Kunstmaan\AdminListBundle\Event\AdminListEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * AdminListController.
 */
abstract class AdminListController extends Controller
{
    /**
     * You can override this method to return the correct entity manager when using multiple databases ...
     *
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    protected function getEntityManager()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * Shows the list of entities.
     *
     * @param AbstractAdminListConfigurator $configurator
     * @param null|Request                  $request
     *
     * @return Response
     */
    protected function doIndexAction(AbstractAdminListConfigurator $configurator, Request $request)
    {
        $em = $this->getEntityManager();
        // @var AdminList $adminList
        $adminList = $this->get('kunstmaan_adminlist.factory')->createList($configurator, $em);
        $adminList->bindRequest($request);

        $this->buildSortableFieldActions($configurator);

        return new Response(
            $this->renderView(
                $configurator->getListTemplate(),
                ['adminlist' => $adminList, 'adminlistconfigurator' => $configurator, 'addparams' => []]
            )
        );
    }

    /**
     * Export a list of Entities.
     *
     * @param AbstractAdminListConfigurator $configurator The adminlist configurator
     * @param string                        $_format      The format to export to
     * @param null|Request                  $request
     *
     * @throws AccessDeniedHttpException
     *
     * @return Response
     */
    protected function doExportAction(AbstractAdminListConfigurator $configurator, $_format, Request $request = null)
    {
        if (!$configurator->canExport()) {
            throw new AccessDeniedHttpException('You do not have sufficient rights to access this page.');
        }

        $em = $this->getEntityManager();

        // @var AdminList $adminList
        $adminList = $this->get('kunstmaan_adminlist.factory')->createExportList($configurator, $em);
        $adminList->bindRequest($request);

        return $this->get('kunstmaan_adminlist.service.export')->getDownloadableResponse($adminList, $_format);
    }

    /**
     * Sets pagesize.
     *
     * @param AbstractAdminListConfigurator $configurator
     * @param null|Request                  $request
     *
     * @return Response
     */
    protected function doSetPagesizeAction(AbstractAdminListConfigurator $configurator, Request $request)
    {
        $em = $this->getEntityManager();
        // @var AdminList $adminList
        $adminList = $this->get('kunstmaan_adminlist.factory')->createList($configurator, $em);
        if ($request->query->has('pagesize')) {
            $adminList->bindRequest($request);
        }

        return $this->redirectToRoute($adminList->getIndexUrl()['path']);
    }

    /**
     * Creates and processes the form to add a new Entity.
     *
     * @param AbstractAdminListConfigurator $configurator The adminlist configurator
     * @param string                        $type         The type to add
     * @param null|Request                  $request
     *
     * @throws AccessDeniedHttpException
     *
     * @return Response
     */
    protected function doAddAction(AbstractAdminListConfigurator $configurator, $type, Request $request)
    {
        if (!$configurator->canAdd()) {
            throw new AccessDeniedHttpException('You do not have sufficient rights to access this page.');
        }

        // @var EntityManager $em
        $em = $this->getEntityManager();

        $entityName = null;
        if (isset($type)) {
            $entityName = $type;
        } else {
            $entityName = $configurator->getRepositoryName();
        }

        $classMetaData = $em->getClassMetadata($entityName);
        // Creates a new instance of the mapped class, without invoking the constructor.
        $classname = $classMetaData->getName();
        $helper = new $classname();
        $helper = $configurator->decorateNewEntity($helper);

        $formType = $configurator->getAdminType($helper);

        $event = new AdaptSimpleFormEvent($request, $formType, $helper, $configurator->getAdminTypeOptions());
        $event = $this->container->get('event_dispatcher')->dispatch(Events::ADAPT_SIMPLE_FORM, $event);
        $tabPane = $event->getTabPane();

        $form = $this->createForm($formType, $helper, $configurator->getAdminTypeOptions());

        if ($request->isMethod('POST')) {
            if ($tabPane) {
                $tabPane->bindRequest($request);
                $form = $tabPane->getForm();
            } else {
                $form->handleRequest($request);
            }

            // Don't redirect to listing when coming from ajax request, needed for url chooser.
            if ($form->isSubmitted() && $form->isValid() && !$request->isXmlHttpRequest()) {
                $adminListEvent = new AdminListEvent($helper, $request, $form);
                $this->container->get('event_dispatcher')->dispatch(
                    AdminListEvents::PRE_ADD,
                    $adminListEvent
                )
                ;

                // Check if Response is given
                if ($adminListEvent->getResponse() instanceof Response) {
                    return $adminListEvent->getResponse();
                }

                // Set sort weight
                $helper = $this->setSortWeightOnNewItem($configurator, $helper);

                $em->persist($helper);
                $em->flush();

                $this->get('session')->getFlashbag()->add('success', 'kuma_admin_list.messages.add_success');

                $this->container->get('event_dispatcher')->dispatch(
                    AdminListEvents::POST_ADD,
                    $adminListEvent
                )
                ;

                // Check if Response is given
                if ($adminListEvent->getResponse() instanceof Response) {
                    return $adminListEvent->getResponse();
                }

                $indexUrl = $configurator->getIndexUrl();

                return new RedirectResponse(
                    $this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : [])
                );
            } elseif (!$request->isXmlHttpRequest()) {
                $this->get('session')->getFlashbag()->add('error', 'kuma_admin_list.messages.add_error');
            }
        }

        $params = ['form' => $form->createView(), 'adminlistconfigurator' => $configurator];

        if ($tabPane) {
            $params = array_merge($params, ['tabPane' => $tabPane]);
        }

        return new Response(
            $this->renderView($configurator->getAddTemplate(), $params)
        );
    }

    /**
     * Creates and processes the edit form for an Entity using its ID.
     *
     * @param AbstractAdminListConfigurator $configurator The adminlist configurator
     * @param string                        $entityId     The id of the entity that will be edited
     * @param null|Request                  $request
     *
     * @throws NotFoundHttpException
     * @throws AccessDeniedHttpException
     *
     * @return Response
     */
    protected function doEditAction(AbstractAdminListConfigurator $configurator, $entityId, Request $request)
    {
        // @var EntityManager $em
        $em = $this->getEntityManager();
        $helper = $em->getRepository($configurator->getRepositoryName())->findOneById($entityId);
        if (null === $helper) {
            throw new NotFoundHttpException('Entity not found.');
        }

        if (!$configurator->canEdit($helper)) {
            throw new AccessDeniedHttpException('You do not have sufficient rights to access this page.');
        }

        $formType = $configurator->getAdminType($helper);

        $event = new AdaptSimpleFormEvent($request, $formType, $helper, $configurator->getAdminTypeOptions());
        $event = $this->container->get('event_dispatcher')->dispatch(Events::ADAPT_SIMPLE_FORM, $event);
        $tabPane = $event->getTabPane();

        $form = $this->createForm($formType, $helper, $configurator->getAdminTypeOptions());

        if ($request->isMethod('POST')) {
            if ($tabPane) {
                $tabPane->bindRequest($request);
                $form = $tabPane->getForm();
            } else {
                $form->handleRequest($request);
            }

            // Don't redirect to listing when coming from ajax request, needed for url chooser.
            if ($form->isSubmitted() && $form->isValid() && !$request->isXmlHttpRequest()) {
                $adminListEvent = new AdminListEvent($helper, $request, $form);
                $this->container->get('event_dispatcher')->dispatch(
                    AdminListEvents::PRE_EDIT,
                    $adminListEvent
                )
                ;

                // Check if Response is given
                if ($adminListEvent->getResponse() instanceof Response) {
                    return $adminListEvent->getResponse();
                }

                $em->persist($helper);
                $em->flush();

                $this->get('session')->getFlashbag()->add('success', 'kuma_admin_list.messages.edit_success');

                $this->container->get('event_dispatcher')->dispatch(
                    AdminListEvents::POST_EDIT,
                    $adminListEvent
                )
                ;

                // Check if Response is given
                if ($adminListEvent->getResponse() instanceof Response) {
                    return $adminListEvent->getResponse();
                }

                $indexUrl = $configurator->getIndexUrl();

                // Don't redirect to listing when coming from ajax request, needed for url chooser.
                if (!$request->isXmlHttpRequest()) {
                    return new RedirectResponse(
                        $this->generateUrl(
                            $indexUrl['path'],
                            isset($indexUrl['params']) ? $indexUrl['params'] : []
                        )
                    );
                }
            } elseif (!$request->isXmlHttpRequest()) {
                $this->get('session')->getFlashbag()->add('error', 'kuma_admin_list.messages.edit_error');
            }
        }

        $configurator->buildItemActions();

        $params = ['form' => $form->createView(), 'entity' => $helper, 'adminlistconfigurator' => $configurator];

        if ($tabPane) {
            $params = array_merge($params, ['tabPane' => $tabPane]);
        }

        return new Response(
            $this->renderView(
                $configurator->getEditTemplate(),
                $params
            )
        );
    }

    protected function doViewAction(AbstractAdminListConfigurator $configurator, $entityId, Request $request)
    {
        // @var EntityManager $em
        $em = $this->getEntityManager();
        $helper = $em->getRepository($configurator->getRepositoryName())->findOneById($entityId);
        if (null === $helper) {
            throw new NotFoundHttpException('Entity not found.');
        }

        if (!$configurator->canView($helper)) {
            throw new AccessDeniedHttpException('You do not have sufficient rights to access this page.');
        }

        $MetaData = $em->getClassMetadata($configurator->getRepositoryName());
        $fields = [];
        $accessor = PropertyAccess::createPropertyAccessor();
        foreach ($MetaData->fieldNames as $value) {
            $fields[$value] = $accessor->getValue($helper, $value);
        }

        return new Response(
            $this->renderView(
                $configurator->getViewTemplate(),
                ['entity' => $helper, 'adminlistconfigurator' => $configurator, 'fields' => $fields]
            )
        );
    }

    /**
     * Delete the Entity using its ID.
     *
     * @param AbstractAdminListConfigurator $configurator The adminlist configurator
     * @param int                           $entityId     The id to delete
     * @param null|Request                  $request
     *
     * @throws NotFoundHttpException
     * @throws AccessDeniedHttpException
     *
     * @return Response
     */
    protected function doDeleteAction(AbstractAdminListConfigurator $configurator, $entityId, Request $request)
    {
        // @var $em EntityManager
        $em = $this->getEntityManager();
        $helper = $em->getRepository($configurator->getRepositoryName())->findOneById($entityId);
        if (null === $helper) {
            throw new NotFoundHttpException('Entity not found.');
        }
        if (!$configurator->canDelete($helper)) {
            throw new AccessDeniedHttpException('You do not have sufficient rights to access this page.');
        }

        $indexUrl = $configurator->getIndexUrl();
        if ($request->isMethod('POST')) {
            $adminListEvent = new AdminListEvent($helper, $request);
            $this->container->get('event_dispatcher')->dispatch(
                AdminListEvents::PRE_DELETE,
                $adminListEvent
            )
            ;

            // Check if Response is given
            if ($adminListEvent->getResponse() instanceof Response) {
                return $adminListEvent->getResponse();
            }

            $em->remove($helper);
            $em->flush();

            $this->get('session')->getFlashbag()->add('success', 'kuma_admin_list.messages.delete_success');

            $this->container->get('event_dispatcher')->dispatch(
                AdminListEvents::POST_DELETE,
                $adminListEvent
            )
            ;

            // Check if Response is given
            if ($adminListEvent->getResponse() instanceof Response) {
                return $adminListEvent->getResponse();
            }
        }

        return new RedirectResponse(
            $this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : [])
        );
    }

    /**
     * Move an item up in the list.
     *
     * @param mixed $entityId
     *
     * @return RedirectResponse
     */
    protected function doMoveUpAction(AbstractAdminListConfigurator $configurator, $entityId, Request $request)
    {
        $em = $this->getEntityManager();
        $sortableField = $configurator->getSortableField();
        $repo = $em->getRepository($configurator->getRepositoryName());
        $item = $repo->find($entityId);

        $setter = 'set'.ucfirst($sortableField);
        $getter = 'get'.ucfirst($sortableField);

        $nextItem = $repo->createQueryBuilder('i')
                         ->where('i.'.$sortableField.' < :weight')
                         ->setParameter('weight', $item->$getter())
                         ->orderBy('i.'.$sortableField, 'DESC')
                         ->setMaxResults(1)
                         ->getQuery()
                         ->getOneOrNullResult()
        ;
        if ($nextItem) {
            $nextItem->$setter($item->$getter());
            $em->persist($nextItem);
            $item->$setter($item->$getter() - 1);

            $em->persist($item);
            $em->flush();
        }

        $indexUrl = $configurator->getIndexUrl();

        return new RedirectResponse(
            $this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : [])
        );
    }

    protected function doMoveDownAction(AbstractAdminListConfigurator $configurator, $entityId, Request $request)
    {
        $em = $this->getEntityManager();
        $sortableField = $configurator->getSortableField();
        $repo = $em->getRepository($configurator->getRepositoryName());
        $item = $repo->find($entityId);

        $setter = 'set'.ucfirst($sortableField);
        $getter = 'get'.ucfirst($sortableField);

        $nextItem = $repo->createQueryBuilder('i')
                         ->where('i.'.$sortableField.' > :weight')
                         ->setParameter('weight', $item->$getter())
                         ->orderBy('i.'.$sortableField, 'ASC')
                         ->setMaxResults(1)
                         ->getQuery()
                         ->getOneOrNullResult()
        ;
        if ($nextItem) {
            $nextItem->$setter($item->$getter());
            $em->persist($nextItem);
            $item->$setter($item->$getter() + 1);

            $em->persist($item);
            $em->flush();
        }

        $indexUrl = $configurator->getIndexUrl();

        return new RedirectResponse(
            $this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : [])
        );
    }

    /**
     * Sets the sort weight on a new item. Can be overridden if a non-default sorting implementation is being used.
     *
     * @param AbstractAdminListConfigurator $configurator The adminlist configurator
     * @param $item
     *
     * @return mixed
     */
    protected function setSortWeightOnNewItem(AbstractAdminListConfigurator $configurator, $item)
    {
        if ($configurator instanceof SortableInterface) {
            $repo = $this->getEntityManager()->getRepository($configurator->getRepositoryName());
            $sort = $configurator->getSortableField();
            $weight = $this->getMaxSortableField($repo, $sort);
            $setter = 'set'.ucfirst($sort);
            $item->$setter($weight + 1);
        }

        return $item;
    }

    protected function buildSortableFieldActions(AbstractAdminListConfigurator $configurator)
    {
        // Check if Sortable interface is implemented
        if ($configurator instanceof SortableInterface) {
            $route = function (EntityInterface $item) use ($configurator) {
                return [
                    'path' => $configurator->getPathByConvention().'_move_up',
                    'params' => ['id' => $item->getId()],
                ];
            };

            $action = new SimpleItemAction($route, 'arrow-up', 'Move up');
            $configurator->addItemAction($action);

            $route = function (EntityInterface $item) use ($configurator) {
                return [
                    'path' => $configurator->getPathByConvention().'_move_down',
                    'params' => ['id' => $item->getId()],
                ];
            };

            $action = new SimpleItemAction($route, 'arrow-down', 'Move down');
            $configurator->addItemAction($action);
        }
    }

    private function getMaxSortableField($repo, $sort)
    {
        $maxWeight = $repo->createQueryBuilder('i')
                          ->select('max(i.'.$sort.')')
                          ->getQuery()
                          ->getSingleScalarResult()
        ;

        return (int) $maxWeight;
    }
}
