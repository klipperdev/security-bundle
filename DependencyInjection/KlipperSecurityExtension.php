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

use Klipper\Bundle\SecurityBundle\DependencyInjection\Extension\AnnotationBuilder;
use Klipper\Bundle\SecurityBundle\DependencyInjection\Extension\AnonymousRoleBuilder;
use Klipper\Bundle\SecurityBundle\DependencyInjection\Extension\ExpressionLanguageBuilder;
use Klipper\Bundle\SecurityBundle\DependencyInjection\Extension\ExtensionBuilderInterface;
use Klipper\Bundle\SecurityBundle\DependencyInjection\Extension\HostRoleBuilder;
use Klipper\Bundle\SecurityBundle\DependencyInjection\Extension\ModelBuilder;
use Klipper\Bundle\SecurityBundle\DependencyInjection\Extension\ObjectFilterBuilder;
use Klipper\Bundle\SecurityBundle\DependencyInjection\Extension\OrganizationalContextBuilder;
use Klipper\Bundle\SecurityBundle\DependencyInjection\Extension\PermissionBuilder;
use Klipper\Bundle\SecurityBundle\DependencyInjection\Extension\RoleHierarchyBuilder;
use Klipper\Bundle\SecurityBundle\DependencyInjection\Extension\SecurityIdentityBuilder;
use Klipper\Bundle\SecurityBundle\DependencyInjection\Extension\SecurityVoterBuilder;
use Klipper\Bundle\SecurityBundle\DependencyInjection\Extension\SharingBuilder;
use Klipper\Bundle\SecurityBundle\DependencyInjection\Extension\ValidatorBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * The extension that fulfills the infos for the container from configuration.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class KlipperSecurityExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        foreach ($this->getExtensionBuilders() as $extensionBuilder) {
            $extensionBuilder->build($container, $loader, $config);
        }
    }

    /**
     * Get the extension builders.
     *
     * @return ExtensionBuilderInterface[]
     */
    private function getExtensionBuilders(): array
    {
        return [
            new ModelBuilder($this->getAlias()),
            new SecurityIdentityBuilder(),
            new PermissionBuilder(),
            new ObjectFilterBuilder(),
            new HostRoleBuilder(),
            new AnonymousRoleBuilder(),
            new RoleHierarchyBuilder(),
            new SecurityVoterBuilder(),
            new OrganizationalContextBuilder(),
            new ExpressionLanguageBuilder(),
            new SharingBuilder(),
            new ValidatorBuilder(),
            new AnnotationBuilder($this),
        ];
    }
}
