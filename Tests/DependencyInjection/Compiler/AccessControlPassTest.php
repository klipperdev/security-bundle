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

use Klipper\Bundle\SecurityBundle\DependencyInjection\Compiler\AccessControlPass;
use Klipper\Bundle\SecurityBundle\Tests\Fixtures\TestProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Security\Http\AccessMap;

/**
 * Access Control Pass tests.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 *
 * @internal
 */
final class AccessControlPassTest extends TestCase
{
    protected ?ContainerBuilder $container = null;

    protected ?AccessControlPass $compiler = null;

    protected array $accessControl = [
        [
            'path' => '^/path/',
            'allow_if' => 'is_granted("ROLE_ADMIN") and identity("input")',
            'requires_channel' => null,
            'host' => null,
            'ips' => [],
            'methods' => ['GET'],
            'roles' => [],
        ],
        [
            'path' => '^/path/',
            'allow_if' => 'is_granted("ROLE_ADMIN") and identity("input")',
            'requires_channel' => null,
            'host' => null,
            'ips' => [],
            'methods' => ['GET'],
            'roles' => [],
        ],
    ];

    protected function setUp(): void
    {
        $this->container = new ContainerBuilder();
        $this->compiler = new AccessControlPass();

        $accessMapDef = new Definition(AccessMap::class);
        $accessMapDef->setPublic(false);

        $expressionDef = new Definition(TestProvider::class);
        $expressionDef->setPublic(false);
        $expressionDef->addTag('security.expression_language_provider');

        $this->container->setDefinition('security.access_map', $accessMapDef);
        $this->container->setDefinition('security.expression.custom_identity_function', $expressionDef);
    }

    public function testProcessWithoutAccessControl(): void
    {
        /** @var ContainerBuilder|MockObject $container */
        $container = $this->getMockBuilder(ContainerBuilder::class)->disableOriginalConstructor()->getMock();
        $container->expects(static::once())
            ->method('hasParameter')
            ->with('klipper_security.access_control')
            ->willReturn(false)
        ;

        $this->compiler->process($container);
    }

    public function testProcess(): void
    {
        $this->container->setParameter('klipper_security.access_control', $this->accessControl);

        $this->compiler->process($this->container);

        static::assertFalse($this->container->hasParameter('klipper_security.access_control'));
    }
}
