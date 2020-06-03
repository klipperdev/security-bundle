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

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Abstract factory for role injection in security identity manager.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
abstract class AbstractRoleFactory implements SecurityFactoryInterface
{
    public function create(ContainerBuilder $container, string $id, array $config, string $userProvider, ?string $defaultEntryPoint): array
    {
        $providerId = $this->getServiceId('provider').'.'.$id;
        $container
            ->setDefinition($providerId, new ChildDefinition($this->getServiceId('provider')))
        ;

        $listenerId = $this->getServiceId('listener').'.'.$id;
        $container
            ->setDefinition($listenerId, new ChildDefinition($this->getServiceId('listener')))
            ->replaceArgument(1, $config)
        ;

        return [$providerId, $listenerId, $defaultEntryPoint];
    }

    public function getPosition(): string
    {
        return 'pre_auth';
    }

    /**
     * Get the service id.
     */
    protected function getServiceId(string $type): string
    {
        return sprintf('klipper_security.authentication.%s.%s', $type, $this->getKey());
    }
}
