<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Bundle\SecurityBundle\DependencyInjection;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AuthenticatorFactoryInterface;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\UserProvider\UserProviderFactoryInterface;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension as BaseSecurityExtension;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Enhances the access_control section of the SecurityBundle.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class SecurityExtension extends BaseSecurityExtension
{
    private BaseSecurityExtension $extension;

    /**
     * @param BaseSecurityExtension $extension The Symfony Security Extension
     */
    public function __construct(BaseSecurityExtension $extension)
    {
        $this->extension = $extension;
    }

    public function getAlias(): string
    {
        return $this->extension->getAlias();
    }

    public function getNamespace(): string
    {
        return $this->extension->getNamespace();
    }

    /**
     * @return false|string
     */
    public function getXsdValidationBasePath()
    {
        return $this->extension->getXsdValidationBasePath();
    }

    /**
     * Get the configuration.
     *
     * @param array            $config    The config
     * @param ContainerBuilder $container The container
     */
    public function getConfiguration(array $config, ContainerBuilder $container): ConfigurationInterface
    {
        return $this->extension->getConfiguration($config, $container);
    }

    /**
     * Add the security factory.
     */
    public function addAuthenticatorFactory(AuthenticatorFactoryInterface $factory): void
    {
        $this->extension->addAuthenticatorFactory($factory);
    }

    /**
     * Add the user provider factory.
     *
     * @param UserProviderFactoryInterface $factory The user provider factory
     */
    public function addUserProviderFactory(UserProviderFactoryInterface $factory): void
    {
        $this->extension->addUserProviderFactory($factory);
    }

    public function prepend(ContainerBuilder $container): void
    {
        // Skip
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $parentConfigs = [];

        foreach ($configs as $config) {
            if (isset($config['rule'])) {
                unset($config['rule']);
            }

            if (isset($config['access_control'])) {
                unset($config['access_control']);
            }

            $parentConfigs[] = $config;
        }

        $this->extension->load($parentConfigs, $container);
        $this->createAuthorization($configs, $container);
    }

    /**
     * Create the authorization.
     *
     * @param array            $configs   The configs
     * @param ContainerBuilder $container The container
     */
    private function createAuthorization(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new AccessControlConfiguration(), $configs);

        if (!$config['access_control']) {
            return;
        }

        $container->setParameter('klipper_security.access_control', $config['access_control']);
    }
}
