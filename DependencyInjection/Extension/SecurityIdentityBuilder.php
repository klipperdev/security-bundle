<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Bundle\SecurityBundle\DependencyInjection\Extension;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class SecurityIdentityBuilder implements ExtensionBuilderInterface
{
    /**
     * @throws
     */
    public function build(ContainerBuilder $container, LoaderInterface $loader, array $config): void
    {
        $loader->load('security_identity.xml');
    }
}
