<?php

namespace Kunstmaan\MultiDomainBundle\Twig;

use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;

class MultiDomainTwigExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
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
            new \Twig_SimpleFunction('get_multi_domain_hosts', [$this, 'getMultiDomainHosts']),
            new \Twig_SimpleFunction('get_current_host', [$this, 'getCurrentHost']),
            new \Twig_SimpleFunction('get_extra_data', [$this, 'getExtraData']),
            new \Twig_SimpleFunction('get_current_full_host', [$this, 'getCurrentFullHost']),
        ];
    }

    /**
     * @param $key
     */
    public function getExtraData($key)
    {
        $extraData = $this->domainConfiguration->getExtraData();

        if ($extraData[$key]) {
            return $extraData[$key];
        }

        return null;
    }

    /**
     * @return array
     */
    public function getMultiDomainHosts()
    {
        return $this->domainConfiguration->getHosts();
    }

    /**
     * @return string
     */
    public function getCurrentHost()
    {
        return $this->domainConfiguration->getHost();
    }

    /**
     * @return array
     */
    public function getCurrentFullHost()
    {
        return $this->domainConfiguration->getFullHost();
    }
}
