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
class HostRoleBuilder implements ExtensionBuilderInterface
{
    /**
     * @throws
     */
    public function build(ContainerBuilder $container, LoaderInterface $loader, array $config): void
    {
        $loader->load('host_role.xml');

        $def = $container->getDefinition('klipper_security.authenticator.host_roles.firewall_listener');
        $def->addMethodCall('setEnabled', [$config['host_role']['enabled']]);
    }
}
