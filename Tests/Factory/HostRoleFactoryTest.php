<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Bundle\SecurityBundle\Tests\Factory;

use Klipper\Bundle\SecurityBundle\Factory\HostRoleFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Host Role Factory Tests.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 *
 * @internal
 */
final class HostRoleFactoryTest extends TestCase
{
    public function testGetPriority(): void
    {
        $factory = new HostRoleFactory();

        static::assertSame(-10, $factory->getPriority());
    }

    public function testGetKey(): void
    {
        $factory = new HostRoleFactory();

        static::assertSame('host_roles', $factory->getKey());
    }

    public function testAddConfiguration(): void
    {
        $builder = new ArrayNodeDefinition('test');
        $factory = new HostRoleFactory();

        $factory->addConfiguration($builder);
        static::assertCount(1, $builder->getChildNodeDefinitions());
    }

    public function testCreateAuthenticator(): void
    {
        $container = new ContainerBuilder();
        $factory = new HostRoleFactory();

        $authenticator = $factory->createAuthenticator($container, 'test_firewall', [], 'user_provider_id');

        static::assertIsArray($authenticator);
        static::assertEmpty($authenticator);
    }

    public function testCreateListeners(): void
    {
        $container = new ContainerBuilder();
        $factory = new HostRoleFactory();

        static::assertCount(1, $container->getDefinitions());

        $res = $factory->createListeners($container, 'test_firewall', []);
        $valid = [
            'klipper_security.authenticator.host_roles.firewall_listener.test_firewall',
        ];

        static::assertEquals($valid, $res);
        static::assertCount(2, $container->getDefinitions());
    }
}
