<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Bundle\SecurityBundle\DependencyInjection\Extension;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
abstract class BuilderUtils
{
    /**
     * Validate the configuration.
     *
     * @param ContainerBuilder $container The container
     * @param string           $config    The name of config
     * @param string           $service   The required service id
     * @param string           $package   The required package name
     */
    public static function validate(ContainerBuilder $container, string $config, string $service, string $package): void
    {
        $missingServices = $container->hasParameter('klipper_security.missing_services')
            ? $container->getParameter('klipper_security.missing_services')
            : [];

        $missingServices[$config] = [$service, $package];
        $container->setParameter('klipper_security.missing_services', $missingServices);
    }

    /**
     * Load the database provider.
     *
     * @param LoaderInterface $loader The config loader
     * @param array           $config The config
     * @param string          $type   The provider type
     *
     * @throws
     */
    public static function loadProvider(LoaderInterface $loader, array $config, string $type): void
    {
        if ('custom' !== $config['db_driver']) {
            $loader->load($config['db_driver'].'_provider_'.$type.'.xml');
        }
    }
}
