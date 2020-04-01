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

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Validate the service dependencies of the configuration.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class ConfigDependencyValidationPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('klipper_security.missing_services')) {
            return;
        }

        $missingServices = $container->getParameter('klipper_security.missing_services');

        foreach ($missingServices as $config => $serviceInfo) {
            list($service, $package) = $serviceInfo;

            if (!$container->hasDefinition($service) && !$container->hasAlias($service)) {
                $msg = 'The "klipper_security.%s" config require the "%s" package';

                throw new InvalidConfigurationException(sprintf($msg, $config, $package));
            }
        }
    }
}
