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

/**
 * Adds all services with the tags "klipper_security.sharing_identity_loader" as arguments
 * of the "klipper_security.sharing_identity_resolver" service.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class SharingIdentityLoaderPass extends AbstractLoaderPass
{
    public function __construct()
    {
        parent::__construct('klipper_security.sharing_identity_resolver', 'klipper_security.sharing_identity_loader');
    }
}
