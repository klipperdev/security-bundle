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

use Klipper\Component\Security\Sharing\SharingIdentityConfig;
use Klipper\Component\Security\Sharing\SharingSubjectConfig;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class SharingBuilder implements ExtensionBuilderInterface
{
    /**
     * @throws
     */
    public function build(ContainerBuilder $container, LoaderInterface $loader, array $config): void
    {
        if ($config['sharing']['enabled']) {
            $loader->load('sharing.xml');
            $this->buildSharingConfigs($container, $config);
            BuilderUtils::loadProvider($loader, $config, 'sharing');
        }

        $this->buildDoctrineSharingFilter($container, $loader, $config);
        $this->buildDoctrineSharingListener($container, $loader, $config);
        $this->buildDoctrineSharingDeleteListener($container, $loader, $config);
    }

    /**
     * Build the sharing configurations.
     *
     * @param ContainerBuilder $container The container
     * @param array            $config    The config
     */
    private function buildSharingConfigs(ContainerBuilder $container, array $config): void
    {
        $subjectConfigs = [];
        $identityConfigs = [];

        foreach ($config['sharing']['subjects'] as $type => $subjectConfig) {
            $subjectConfigs[] = $this->buildSharingSubjectConfig($container, $type, $subjectConfig);
        }

        foreach ($config['sharing']['identity_types'] as $type => $identityConfig) {
            $identityConfigs[] = $this->buildSharingIdentityConfig($container, $type, $identityConfig);
        }

        $container->getDefinition('klipper_security.sharing_subject_loader.configuration')
            ->replaceArgument(0, $subjectConfigs)
        ;

        $container->getDefinition('klipper_security.sharing_identity_loader.configuration')
            ->replaceArgument(0, $identityConfigs)
        ;
    }

    /**
     * Build the doctrine sharing filter.
     *
     * @param ContainerBuilder $container The container
     * @param LoaderInterface  $loader    The config loader
     * @param array            $config    The config
     *
     * @throws
     */
    private function buildDoctrineSharingFilter(
        ContainerBuilder $container,
        LoaderInterface $loader,
        array $config
    ): void {
        if ($config['doctrine']['orm']['filters']['sharing']) {
            BuilderUtils::validate($container, 'doctrine.orm.filter.sharing', 'doctrine.orm.entity_manager', 'doctrine/orm');

            if (!$config['sharing']['enabled']) {
                throw new InvalidConfigurationException('The "klipper_security.sharing" config must be enabled');
            }

            $loader->load('orm_filter_sharing.xml');
        }
    }

    /**
     * Build the doctrine sharing listener.
     *
     * @param ContainerBuilder $container The container
     * @param LoaderInterface  $loader    The config loader
     * @param array            $config    The config
     *
     * @throws
     */
    private function buildDoctrineSharingListener(
        ContainerBuilder $container,
        LoaderInterface $loader,
        array $config
    ): void {
        // doctrine orm sharing filter listener for private sharing
        if ($config['doctrine']['orm']['listeners']['private_sharing']) {
            BuilderUtils::validate($container, 'doctrine.orm.listeners.private_sharing', 'doctrine.orm.entity_manager', 'doctrine/orm');

            if (!$config['doctrine']['orm']['filters']['sharing']) {
                throw new InvalidConfigurationException('The "klipper_security.doctrine.orm.filters.sharing" config must be enabled');
            }

            $loader->load('orm_listener_private_sharing.xml');
        }
    }

    /**
     * Build the doctrine sharing delete listener.
     *
     * @param ContainerBuilder $container The container
     * @param LoaderInterface  $loader    The config loader
     * @param array            $config    The config
     *
     * @throws
     */
    private function buildDoctrineSharingDeleteListener(
        ContainerBuilder $container,
        LoaderInterface $loader,
        array $config
    ): void {
        // doctrine orm sharing delete listener for private sharing
        if ($config['doctrine']['orm']['listeners']['sharing_delete']) {
            BuilderUtils::validate($container, 'doctrine.orm.listeners.sharing_delete', 'doctrine.orm.entity_manager', 'doctrine/orm');

            if (!$config['sharing']['enabled']) {
                throw new InvalidConfigurationException('The "klipper_security.sharing" config must be enabled');
            }

            $loader->load('orm_listener_sharing_delete.xml');
        }
    }

    /**
     * Build the sharing subject config.
     *
     * @param ContainerBuilder $container The container
     * @param string           $type      The sharing subject type
     * @param array            $config    The sharing subject config
     */
    private function buildSharingSubjectConfig(ContainerBuilder $container, string $type, array $config): Reference
    {
        if (!class_exists($type)) {
            $msg = 'The "%s" sharing subject class does not exist';

            throw new InvalidConfigurationException(sprintf($msg, $type));
        }

        $def = new Definition(SharingSubjectConfig::class, [
            $type,
            $config['visibility'],
        ]);
        $def->setPublic(false);

        $id = 'klipper_security.sharing_subject_config.'.strtolower(str_replace('\\', '_', $type));
        $container->setDefinition($id, $def);

        return new Reference($id);
    }

    /**
     * Build the sharing identity config.
     *
     * @param ContainerBuilder $container The container
     * @param string           $type      The sharing identity type
     * @param array            $config    The sharing identity config
     */
    private function buildSharingIdentityConfig(ContainerBuilder $container, string $type, array $config): Reference
    {
        if (!class_exists($type)) {
            $msg = 'The "%s" sharing identity class does not exist';

            throw new InvalidConfigurationException(sprintf($msg, $type));
        }

        $def = new Definition(SharingIdentityConfig::class, [
            $type,
            $config['alias'],
            $config['roleable'],
            $config['permissible'],
        ]);
        $def->setPublic(false);

        $id = 'klipper_security.sharing_identity_config.'.strtolower(str_replace('\\', '_', $type));
        $container->setDefinition($id, $def);

        return new Reference($id);
    }
}
