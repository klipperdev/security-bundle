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

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

/**
 * Factory for host role injection in existing token role.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class HostRoleFactory extends AbstractRoleFactory
{
    public function getKey(): string
    {
        return 'host_roles';
    }

    public function addConfiguration(NodeDefinition $builder): void
    {
        /* @var ArrayNodeDefinition $builder */
        $builder->example(['*.domain.*' => 'ROLE_WEBSITE', '*' => 'ROLE_PUBLIC']);
        $builder->prototype('scalar')->end();
    }
}
