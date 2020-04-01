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

use Doctrine\ORM\Event\OnFlushEventArgs;
use Klipper\Bundle\SecurityBundle\Doctrine\ORM\Listener\ObjectFilterListenerContainerAware;
use Klipper\Component\Security\ObjectFilter\ObjectFilterInterface;
use Klipper\Component\Security\Permission\PermissionManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Object Filter Listener Container Aware Tests.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 *
 * @internal
 */
final class ObjectFilterListenerContainerAwareTest extends TestCase
{
    public function testOnFlush(): void
    {
        /** @var MockObject|OnFlushEventArgs $args */
        $args = $this->getMockBuilder(OnFlushEventArgs::class)->disableOriginalConstructor()->getMock();
        $tokenStorage = $this->getMockBuilder(TokenStorageInterface::class)->getMock();
        $permissionManager = $this->getMockBuilder(PermissionManagerInterface::class)->getMock();
        $objectFilter = $this->getMockBuilder(ObjectFilterInterface::class)->getMock();
        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();

        $container->expects(static::at(0))
            ->method('get')
            ->with('security.token_storage')
            ->willReturn($tokenStorage)
        ;

        $container->expects(static::at(1))
            ->method('get')
            ->with('klipper_security.permission_manager')
            ->willReturn($permissionManager)
        ;

        $container->expects(static::at(2))
            ->method('get')
            ->with('klipper_security.object_filter')
            ->willReturn($objectFilter)
        ;

        $listener = new ObjectFilterListenerContainerAware();
        $listener->container = $container;

        $listener->onFlush($args);

        static::assertNull($listener->container);
    }
}
