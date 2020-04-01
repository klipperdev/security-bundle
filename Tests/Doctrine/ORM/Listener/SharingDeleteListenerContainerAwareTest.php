<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Bundle\SecurityBundle\Tests\Doctrine\ORM\Listener;

use Klipper\Bundle\SecurityBundle\Doctrine\ORM\Listener\SharingDeleteListenerContainerAware;
use Klipper\Component\Security\Sharing\SharingManagerInterface;
use Klipper\Component\Security\Tests\Fixtures\Model\MockSharing;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Sharing Delete Listener Container Aware Tests.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 *
 * @internal
 */
final class SharingDeleteListenerContainerAwareTest extends TestCase
{
    public function testGetPermissionManager(): void
    {
        $sharingManager = $this->getMockBuilder(SharingManagerInterface::class)->getMock();
        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();

        $container->expects(static::at(0))
            ->method('get')
            ->with('klipper_security.sharing_manager')
            ->willReturn($sharingManager)
        ;

        $listener = new SharingDeleteListenerContainerAware(MockSharing::class);
        $listener->container = $container;

        static::assertSame($sharingManager, $listener->getSharingManager());
        static::assertNull($listener->container);
    }
}
