<?php

namespace Kunstmaan\MultiDomainBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link * http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KunstmaanMultiDomainExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $hostConfigurations = $this->getHostConfigurations($config['hosts']);

        $container->setParameter(
            'kunstmaan_multi_domain.hosts',
            $hostConfigurations
        );

        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yml');

        /*
         * We override the default slug router here. You can use a custom one by
         * setting kunstmaan_multi_domain.router.class to your own implementation.
         */
        $container->setParameter(
            'kunstmaan_node.slugrouter.class',
            $container->getParameter('kunstmaan_multi_domain.router.class')
        );

        /*
         * We override the default domain configuration service here. You can use a custom one by
         * setting kunstmaan_multi_domain.domain_configuration.class to your own implementation.
         */
        $container->setParameter(
            'kunstmaan_admin.domain_configuration.class',
            $container->getParameter('kunstmaan_multi_domain.domain_configuration.class')
        );
    }

    /**
     * Convert config hosts array to a usable format.
     *
     * @param $hosts
     *
     * @return array
     */
    private function getHostConfigurations($hosts)
    {
        $hostConfigurations = [];
        foreach ($hosts as $name => $settings) {
            $host = $settings['host'];
            // Set the key of the host as id.
            $hostConfigurations[$host]['id'] = $name;

            foreach ($settings as $setting => $data) {
                if ('locales' === $setting) {
                    $hostConfigurations[$host]['locales_extra'] = $this->getLocalesExtra($data);
                    $data = $this->getHostLocales($data);
                    $hostConfigurations[$host]['reverse_locales'] = array_flip($data);
                }
                $hostConfigurations[$host][$setting] = $data;
            }
        }

        return $hostConfigurations;
    }

    /**
     * Return uri to actual locale mappings.
     *
     * @param $localeSettings
     *
     * @return array
     */
    private function getHostLocales($localeSettings)
    {
        $hostLocales = [];
        foreach ($localeSettings as $key => $localeMapping) {
            $hostLocales[$localeMapping['uri_locale']] = $localeMapping['locale'];
        }

        return $hostLocales;
    }

    /**
     * Return the extra data configured for each locale.
     *
     * @param $localeSettings
     *
     * @return array
     */
    private function getLocalesExtra($localeSettings)
    {
        $localesExtra = [];
        foreach ($localeSettings as $key => $localeMapping) {
            $localesExtra[$localeMapping['uri_locale']] = array_key_exists('extra', $localeMapping) ? $localeMapping['extra'] : [];
        }

        return $localesExtra;
    }
}
