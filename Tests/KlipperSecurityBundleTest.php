<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Bundle\SecurityBundle\Tests;

use Klipper\Bundle\SecurityBundle\KlipperSecurityBundle;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Security bundle tests.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 *
 * @internal
 */
final class KlipperSecurityBundleTest extends TestCase
{
    public function testSecurityBundleNotRegistered(): void
    {
        $this->expectException(\Klipper\Component\Security\Exception\LogicException::class);
        $this->expectExceptionMessage('The KlipperSecurityBundle must be registered after the SecurityBundle in your App Kernel');

        /** @var ContainerBuilder|MockObject $container */
        $container = $this->getMockBuilder(ContainerBuilder::class)->getMock();
        $container->expects(static::once())
            ->method('hasExtension')
            ->with('security')
            ->willReturn(false)
        ;

        $bundle = new KlipperSecurityBundle();
        $bundle->build($container);
    }
}
