<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Bundle\SecurityBundle\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AuthenticatorFactoryInterface;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\FirewallListenerFactoryInterface;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Abstract factory for role injection in security identity manager.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
abstract class AbstractRoleFactory implements AuthenticatorFactoryInterface, FirewallListenerFactoryInterface
{
    public function getPriority(): int
    {
        return -10;
    }

    public function createAuthenticator(ContainerBuilder $container, string $firewallName, array $config, string $userProviderId): array
    {
        return [];
    }

    public function createListeners(ContainerBuilder $container, string $firewallName, array $config): array
    {
        $listenerId = 'klipper_security.authenticator.'.$this->getKey().'.firewall_listener.'.$firewallName;
        $container
            ->setDefinition($listenerId, new ChildDefinition('klipper_security.authenticator.'.$this->getKey().'.firewall_listener'))
            ->replaceArgument(1, $config)
        ;

        return [$listenerId];
    }
}
