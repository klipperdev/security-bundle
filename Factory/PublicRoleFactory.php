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
 * Factory for public role injection in security identity manager.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class PublicRoleFactory extends AbstractRoleFactory
{
    public function getKey(): string
    {
        return 'public_role';
    }

    public function addConfiguration(NodeDefinition $builder): void
    {
        /* @var ArrayNodeDefinition $builder */
        $builder
            ->example('ROLE_CUSTOM_PUBLIC')
            ->addDefaultsIfNotSet()
            ->beforeNormalization()
            ->ifTrue(static function ($v) {
                return \is_bool($v) || \is_string($v);
            })
            ->then(function ($v) {
                return ['role' => $this->getPublicRole($v)];
            })
            ->end()
            ->children()
            ->scalarNode('role')->defaultNull()->end()
            ->end()
        ;
    }

    /**
     * @param null|bool|string $v
     */
    private function getPublicRole($v): ?string
    {
        if (true === $v) {
            $v = 'ROLE_PUBLIC';
        }

        return \is_string($v)
            ? $v
            : null;
    }
}
