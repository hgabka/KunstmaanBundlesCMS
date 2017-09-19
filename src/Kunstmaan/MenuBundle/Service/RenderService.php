<?php

namespace Kunstmaan\MenuBundle\Service;

use Kunstmaan\MenuBundle\Entity\MenuItem;
use Symfony\Component\Routing\RouterInterface;

class RenderService
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param \Twig_Environment $environment
     * @param $node
     * @param array $options
     *
     * @return string
     */
    public function renderMenuItemTemplate(\Twig_Environment $environment, $node, $options = [])
    {
        $template = isset($options['template']) ? $options['template'] : false;
        if (false === $template) {
            $template = 'KunstmaanMenuBundle::menu-item.html.twig';
        }

        $active = false;
        if (MenuItem::TYPE_PAGE_LINK === $node['type']) {
            $url = $this->router->generate('_slug', ['url' => $node['nodeTranslation']['url']]);

            if ($this->router->getContext()->getPathInfo() === $url) {
                $active = true;
            }
        } else {
            $url = $node['url'];
        }

        if (MenuItem::TYPE_PAGE_LINK === $node['type']) {
            if ($node['title']) {
                $title = $node['title'];
            } else {
                $title = $node['nodeTranslation']['title'];
            }
        } else {
            $title = $node['title'];
        }

        return $environment->render($template, [
            'menuItem' => $node,
            'url' => $url,
            'options' => $options,
            'title' => $title,
            'active' => $active,
        ]);
    }
}
