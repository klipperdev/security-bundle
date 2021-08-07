<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Bundle\SecurityBundle\DependencyInjection\Compiler;

use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;

/**
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class SecurityRegisterListenerPass extends RegisterListenersPass
{
    public function __construct()
    {
        parent::__construct();

        $this->dispatcherService = 'event_dispatcher';
        $this->listenerTag = 'klipper_security.event_listener';
        $this->subscriberTag = 'klipper_security.event_subscriber';
    }
}
