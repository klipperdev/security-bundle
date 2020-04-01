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
class SecurityVoterBuilder implements ExtensionBuilderInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws
     */
    public function build(ContainerBuilder $container, LoaderInterface $loader, array $config): void
    {
        if ($config['security_voter']['role']) {
            $loader->load('security_voter_role.xml');
        }

        if ($config['security_voter']['group']) {
            $loader->load('security_voter_group.xml');
        }

        if ($container->hasDefinition('klipper_security.access.permission_voter')) {
            $container->getDefinition('klipper_security.access.permission_voter')
                ->replaceArgument(2, $config['security_voter']['allow_not_managed_subject'])
            ;
        }
    }
}
