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

use Klipper\Component\Security\Doctrine\ORM\Listener\PermissionCheckerListener;
use Klipper\Component\Security\Permission\PermissionManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * This class listens to all database activity and automatically adds constraints as permissions.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class PermissionCheckerListenerContainerAware extends PermissionCheckerListener
{
    public ?ContainerInterface $container = null;

    protected function init(): void
    {
        if (null !== $this->container) {
            /** @var TokenStorageInterface $tokenStorage */
            $tokenStorage = $this->container->get('security.token_storage');
            /** @var AuthorizationCheckerInterface $authChecker */
            $authChecker = $this->container->get('security.authorization_checker');
            /** @var PermissionManagerInterface $permManager */
            $permManager = $this->container->get('klipper_security.permission_manager');

            $this->setTokenStorage($tokenStorage);
            $this->setAuthorizationChecker($authChecker);
            $this->setPermissionManager($permManager);
            $this->initialized = true;
            $this->container = null;
        }
    }
}
