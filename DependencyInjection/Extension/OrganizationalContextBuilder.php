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
class OrganizationalContextBuilder implements ExtensionBuilderInterface
{
    /**
     * @throws
     */
    public function build(ContainerBuilder $container, LoaderInterface $loader, array $config): void
    {
        if ($config['organizational_context']['enabled']) {
            $loader->load('organizational_context.xml');
            $loader->load('orm_provider_organizational_user.xml');

            $id = 'klipper_security.organizational_context.service_id';
            $container->setParameter($id, $config['organizational_context']['service_id']);
        }
    }
}
