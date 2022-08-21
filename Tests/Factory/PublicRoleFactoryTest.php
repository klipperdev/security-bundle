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

use Klipper\Bundle\SecurityBundle\Factory\PublicRoleFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Public Role Factory Tests.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 *
 * @internal
 */
final class PublicRoleFactoryTest extends TestCase
{
    public function testGetPriority(): void
    {
        $factory = new PublicRoleFactory();

        static::assertSame(-10, $factory->getPriority());
    }

    public function testGetKey(): void
    {
        $factory = new PublicRoleFactory();

        static::assertSame('public_role', $factory->getKey());
    }

    public function getConfiguration(): array
    {
        return [
            [true, 'ROLE_PUBLIC'],
            [false, null],
            [null, null],
            [['role' => 'ROLE_CUSTOM_PUBLIC'], 'ROLE_CUSTOM_PUBLIC'],
            [['role' => null], null],
        ];
    }

    /**
     * @dataProvider getConfiguration
     *
     * @param null|array|bool $config   The config
     * @param null|string     $expected The expected value
     */
    public function testAddConfiguration($config, ?string $expected): void
    {
        $builder = new ArrayNodeDefinition('public_role');
        $factory = new PublicRoleFactory();

        $factory->addConfiguration($builder);
        static::assertCount(1, $builder->getChildNodeDefinitions());

        $processor = new Processor();
        $res = $processor->process($builder->getNode(), [$config]);

        $value = \is_array($res);
        static::assertTrue($value);
        static::assertArrayHasKey('role', $res);
        static::assertSame($expected, $res['role']);
    }

    public function testCreateAuthenticator(): void
    {
        $container = new ContainerBuilder();
        $factory = new PublicRoleFactory();

        $authenticator = $factory->createAuthenticator($container, 'test_firewall', [], 'user_provider_id');

        static::assertIsArray($authenticator);
        static::assertEmpty($authenticator);
    }

    public function testCreateListeners(): void
    {
        $container = new ContainerBuilder();
        $factory = new PublicRoleFactory();

        static::assertCount(1, $container->getDefinitions());

        $res = $factory->createListeners($container, 'test_firewall', []);
        $valid = [
            'klipper_security.authenticator.public_role.firewall_listener.test_firewall',
        ];

        static::assertEquals($valid, $res);
        static::assertCount(2, $container->getDefinitions());
    }
}
