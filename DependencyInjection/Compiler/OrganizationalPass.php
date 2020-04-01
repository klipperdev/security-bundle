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

use Klipper\Component\Security\Organizational\OrganizationalContextInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * Configure the organizational context service.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class OrganizationalPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        /** @var ParameterBag $pb */
        $pb = $container->getParameterBag();
        $alias = 'klipper_security.organizational_context';
        $serviceIdName = 'klipper_security.organizational_context.service_id';

        if (!$container->hasAlias($alias) || !$container->hasParameter($serviceIdName)) {
            return;
        }

        $serviceId = $container->getParameter($serviceIdName);

        if (null !== $serviceId) {
            $serviceId = $this->getServiceId($container, $serviceId);
            $container->setAlias('klipper_security.organizational_context', new Alias($serviceId, true));
            $container->removeDefinition('klipper_security.organizational_context.default');
            $pb->remove('klipper_security.organizational_context.default.class');
        }

        $pb->remove($serviceIdName);
    }

    /**
     * Get the service id of organizational context.
     *
     * @param ContainerBuilder $container The container
     * @param string           $serviceId The service id defined in config
     *
     * @return string
     */
    protected function getServiceId(ContainerBuilder $container, string $serviceId): string
    {
        $definition = $container->getDefinition($serviceId);
        $interfaces = class_implements($definition->getClass());

        if (!\in_array(OrganizationalContextInterface::class, $interfaces, true)) {
            $msg = 'The service "%s" must implement the %s';

            throw new InvalidConfigurationException(sprintf($msg, $serviceId, OrganizationalContextInterface::class));
        }

        return $serviceId;
    }
}
