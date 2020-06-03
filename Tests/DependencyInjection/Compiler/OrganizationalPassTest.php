<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Bundle\SecurityBundle\Tests\DependencyInjection\Compiler;

use Klipper\Bundle\SecurityBundle\DependencyInjection\Compiler\OrganizationalPass;
use Klipper\Component\Security\Organizational\OrganizationalContext;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Organizational Pass tests.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 *
 * @internal
 */
final class OrganizationalPassTest extends TestCase
{
    public function testProcessWithoutService(): void
    {
        $container = new ContainerBuilder();
        $compiler = new OrganizationalPass();

        static::assertCount(1, $container->getDefinitions());
        $compiler->process($container);
        static::assertCount(1, $container->getDefinitions());
    }

    public function testProcess(): void
    {
        $container = new ContainerBuilder();
        $compiler = new OrganizationalPass();
        $serviceIdName = 'klipper_security.organizational_context.service_id';
        $serviceIdDefault = 'klipper_security.organizational_context.default';
        $serviceId = 'test';

        $container->setParameter($serviceIdName, $serviceId);
        $container->setAlias('klipper_security.organizational_context', $serviceId);

        $defDefault = new Definition(OrganizationalContext::class);
        $container->setDefinition($serviceIdDefault, $defDefault);

        $def = new Definition(OrganizationalContext::class);
        $container->setDefinition($serviceId, $def);

        $compiler->process($container);

        static::assertTrue($container->hasAlias('klipper_security.organizational_context'));
        static::assertTrue($container->hasDefinition($serviceId));
        static::assertFalse($container->hasDefinition($serviceIdDefault));
        static::assertFalse($container->hasParameter($serviceIdName));
    }

    public function testProcessWithInvalidInterface(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The service "test" must implement the Klipper\\Component\\Security\\Organizational\\OrganizationalContextInterface');

        $container = new ContainerBuilder();
        $compiler = new OrganizationalPass();
        $serviceIdName = 'klipper_security.organizational_context.service_id';
        $serviceId = 'test';

        $container->setParameter($serviceIdName, $serviceId);
        $container->setAlias('klipper_security.organizational_context', $serviceId);

        $def = new Definition(\stdClass::class);
        $container->setDefinition($serviceId, $def);

        $compiler->process($container);
    }
}
