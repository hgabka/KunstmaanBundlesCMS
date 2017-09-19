<?php

namespace Kunstmaan\AdminBundle\Twig;

use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;

class MultiDomainAdminTwigExtension extends \Twig_Extension
{
    /**
     * @var DomainConfigurationInterface
     */
    private $domainConfiguration;

    public function __construct(DomainConfigurationInterface $domainConfiguration)
    {
        $this->domainConfiguration = $domainConfiguration;
    }

    /**
     * Get Twig functions defined in this extension.
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('multidomain_widget', [$this, 'renderWidget'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('is_multidomain_site', [$this, 'isMultiDomainSite']),
            new \Twig_SimpleFunction('get_switched_host', [$this, 'getSwitchedHost']),
            new \Twig_SimpleFunction('switched_host_is_current', [$this, 'switchedHostIsCurrent']),
        ];
    }

    /**
     * Render multidomain switcher widget.
     *
     * @param \Twig_Environment $env
     * @param array             $locales    The locales
     * @param string            $route      The route
     * @param array             $parameters The route parameters
     *
     * @return string
     */
    public function renderWidget(\Twig_Environment $env, $route, array $parameters = [])
    {
        $template = $env->loadTemplate(
            '@KunstmaanAdmin/MultiDomainAdminTwigExtension/widget.html.twig'
        );

        return $template->render(
            array_merge(
                $parameters,
                [
                    'hosts' => $this->getAdminDomainHosts(),
                    'route' => $route,
                ]
            )
        );
    }

    /**
     * Check if site is multiDomain.
     *
     * @return bool
     */
    public function isMultiDomainSite()
    {
        return $this->domainConfiguration->isMultiDomainHost();
    }

    /**
     * @return string
     */
    public function getSwitchedHost()
    {
        return $this->domainConfiguration->getHostSwitched();
    }

    /**
     * @return string
     */
    public function switchedHostIsCurrent()
    {
        return $this->domainConfiguration->getHost() === $this->getSwitchedHost()['host'];
    }

    public function getAdminDomainHosts()
    {
        return $this->domainConfiguration->getHosts();
    }
}
