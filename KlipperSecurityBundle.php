<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Bundle\SecurityBundle;

use Klipper\Bundle\SecurityBundle\DependencyInjection\Compiler\AccessControlPass;
use Klipper\Bundle\SecurityBundle\DependencyInjection\Compiler\ConfigDependencyValidationPass;
use Klipper\Bundle\SecurityBundle\DependencyInjection\Compiler\ExpressionVariableStoragePass;
use Klipper\Bundle\SecurityBundle\DependencyInjection\Compiler\ObjectFilterPass;
use Klipper\Bundle\SecurityBundle\DependencyInjection\Compiler\OrganizationalPass;
use Klipper\Bundle\SecurityBundle\DependencyInjection\Compiler\PermissionLoaderPass;
use Klipper\Bundle\SecurityBundle\DependencyInjection\Compiler\RoleHierarchyVoterPass;
use Klipper\Bundle\SecurityBundle\DependencyInjection\Compiler\SecurityRegisterListenerPass;
use Klipper\Bundle\SecurityBundle\DependencyInjection\Compiler\SharingIdentityLoaderPass;
use Klipper\Bundle\SecurityBundle\DependencyInjection\Compiler\SharingSubjectLoaderPass;
use Klipper\Bundle\SecurityBundle\DependencyInjection\Compiler\TranslatorPass;
use Klipper\Bundle\SecurityBundle\DependencyInjection\Compiler\ValidationPass;
use Klipper\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Klipper\Bundle\SecurityBundle\Factory\HostRoleFactory;
use Klipper\Bundle\SecurityBundle\Factory\PublicRoleFactory;
use Klipper\Component\Security\Exception\LogicException;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension as BaseSecurityExtension;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class KlipperSecurityBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $this->registerSecurityExtension($container);

        $container->addCompilerPass(new ConfigDependencyValidationPass());
        $container->addCompilerPass(new ValidationPass());
        $container->addCompilerPass(new TranslatorPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -64);
        $container->addCompilerPass(new ExpressionVariableStoragePass());
        $container->addCompilerPass(new ObjectFilterPass());
        $container->addCompilerPass(new OrganizationalPass());
        $container->addCompilerPass(new SharingSubjectLoaderPass());
        $container->addCompilerPass(new SharingIdentityLoaderPass());
        $container->addCompilerPass(new PermissionLoaderPass());
        $container->addCompilerPass(new RoleHierarchyVoterPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 100);
        $container->addCompilerPass(new SecurityRegisterListenerPass(), PassConfig::TYPE_BEFORE_REMOVING);
    }

    /**
     * Register and decorate the security extension, and inject the host role listener factory.
     *
     * @param ContainerBuilder $container The container
     */
    private function registerSecurityExtension(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('security')) {
            throw new LogicException('The KlipperSecurityBundle must be registered after the SecurityBundle in your App Kernel');
        }

        /** @var BaseSecurityExtension $extension */
        $extension = $container->getExtension('security');
        $extension->addAuthenticatorFactory(new HostRoleFactory());
        $extension->addAuthenticatorFactory(new PublicRoleFactory());

        $container->registerExtension(new SecurityExtension($extension));
        $container->addCompilerPass(new AccessControlPass());
    }
}
