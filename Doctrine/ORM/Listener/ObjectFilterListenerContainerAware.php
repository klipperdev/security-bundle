<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Bundle\SecurityBundle\Doctrine\ORM\Listener;

use Klipper\Component\Security\Doctrine\ORM\Listener\ObjectFilterListener;
use Klipper\Component\Security\ObjectFilter\ObjectFilterInterface;
use Klipper\Component\Security\Permission\PermissionManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * This class listens to all database activity and automatically adds constraints as permissions.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class ObjectFilterListenerContainerAware extends ObjectFilterListener
{
    /**
     * @var ContainerInterface
     */
    public $container;

    /**
     * {@inheritdoc}
     */
    protected function init(): void
    {
        if (null !== $this->container) {
            /** @var TokenStorageInterface $tokenStorage */
            $tokenStorage = $this->container->get('security.token_storage');
            /** @var PermissionManagerInterface $permManager */
            $permManager = $this->container->get('klipper_security.permission_manager');
            /** @var ObjectFilterInterface $objectFilter */
            $objectFilter = $this->container->get('klipper_security.object_filter');

            $this->setTokenStorage($tokenStorage);
            $this->setPermissionManager($permManager);
            $this->setObjectFilter($objectFilter);
            $this->initialized = true;
            $this->container = null;
        }
    }
}
