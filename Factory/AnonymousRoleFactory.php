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
 * Factory for anonymous role injection in security identity manager.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class AnonymousRoleFactory extends AbstractRoleFactory
{
    /**
     * {@inheritdoc}
     */
    public function getKey(): string
    {
        return 'anonymous_role';
    }

    /**
     * {@inheritdoc}
     */
    public function addConfiguration(NodeDefinition $builder): void
    {
        /* @var ArrayNodeDefinition $builder */
        $builder
            ->example('ROLE_CUSTOM_ANONYMOUS')
            ->addDefaultsIfNotSet()
            ->beforeNormalization()
            ->ifTrue(static function ($v) {
                return \is_bool($v) || \is_string($v);
            })
            ->then(function ($v) {
                return ['role' => $this->getAnonymousRole($v)];
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
    private function getAnonymousRole($v): ?string
    {
        if (true === $v) {
            $v = 'ROLE_ANONYMOUS';
        }

        return \is_string($v)
            ? $v
            : null;
    }
}
