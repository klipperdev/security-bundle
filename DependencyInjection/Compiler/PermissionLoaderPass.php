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
 * Adds all services with the tags "klipper_security.permission_loader" as arguments
 * of the "klipper_security.permission_resolver" service.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class PermissionLoaderPass extends AbstractLoaderPass
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('klipper_security.permission_resolver', 'klipper_security.permission_loader');
    }
}
